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
        // Register Policies — this is the single authorization mechanism
        // used throughout the app (every controller calls
        // $this->authorize(...) against these policies).
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // NOTE: the previous version of this file also defined four
        // Gate::define() closures ('manage-users', 'manage-categories',
        // 'manage-services', 'process-submissions') that duplicated exactly
        // what the Policies above already express, and none of the
        // provided controllers actually called Gate::allows() against
        // them — they were dead code shadowing the real authorization
        // path. Removed. If you do need a quick ad-hoc permission check
        // outside a policy-backed model, prefer calling the existing
        // User::isManagement() / User::canProcessSubmission() methods
        // directly rather than reintroducing a second authorization layer.
    }
}
