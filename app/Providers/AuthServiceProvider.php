<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super Admin & Admin implicitly get all permissions.
        // IMPORTANT: Never call $user->can(...) inside Gate::before(), as that re-invokes Gate::before() infinitely for non-admin users.
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole(['Super Admin', 'Super-Admin', 'super-admin', 'admin', 'Admin', 'Administrator'])) {
                    return true;
                }
            }
            if (in_array((int) ($user->role_id ?? 0), [1, 8], true)) {
                return true;
            }
            return null;
        });
    }
}
