<?php

namespace App\Providers;

use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Submission;
use App\Models\User;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServicePolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Register Policies
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
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
    }
}
