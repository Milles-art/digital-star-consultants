<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceField;
use App\Models\Submission;
use App\Models\User;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServiceFieldPolicy;
use App\Policies\ServicePolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(5)->by(strtolower($request->input('email')).'|'.$request->ip()));
        RateLimiter::for('password-reset', fn (Request $request): Limit => Limit::perMinute(3)->by(strtolower($request->input('email')).'|'.$request->ip()));
        RateLimiter::for('submissions', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('tracking', fn (Request $request): Limit => Limit::perMinute(8)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perMinute(3)->by($request->ip()));

        // Register Policies
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(ServiceField::class, ServiceFieldPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Define Gates for quick permission checks
        Gate::define('manage-users', function ($user) {
            return $user->isManagement();
        });

        Gate::define('manage-categories', function ($user) {
            return $user->isManagement();
        });

        Gate::define('manage-services', function ($user) {
            return $user->isManagement();
        });

        Gate::define('process-submissions', function ($user) {
            return $user->canProcessSubmission();
        });

        View::composer('layouts.app', function (ViewInstance $view): void {
            $services = ServiceCategory::query()
                ->active()
                ->topLevel()
                ->with([
                    'children' => fn ($query) => $query
                        ->active()
                        ->with(['services' => fn ($query) => $query->active()->orderBy('sort_order')]),
                    'services' => fn ($query) => $query->active()->orderBy('sort_order'),
                ])
                ->orderBy('sort_order')
                ->get()
                ->map(function (ServiceCategory $category): array {
                    $items = $category->children->isNotEmpty()
                        ? $category->children->map(fn (ServiceCategory $child): array => [
                            $child->name,
                            route('public.services.index', ['category' => $child->slug]),
                        ])
                        : $category->services->map(fn (Service $service): array => [
                            $service->name,
                            route('public.services.show', $service->slug),
                        ]);

                    return [
                        'icon' => $category->icon ?? '',
                        'title' => $category->name,
                        'blurb' => $category->description ?? '',
                        'items' => $items->all(),
                    ];
                })
                ->all();

            $view->with([
                'services' => $services,
                'navLinks' => [
                    ['Home', '/'],
                    ['About us', '/about'],
                    ['Why us', '/why-us'],
                    ['FAQ', '/faq'],
                    ['Contact', '/contact'],
                ],
                'starPath' => 'M12 2l2.9 6.1 6.6.9-4.8 4.7 1.2 6.6-5.9-3.1-5.9 3.1 1.2-6.6-4.8-4.7 6.6-.9L12 2z',
            ]);
        });
    }
}
