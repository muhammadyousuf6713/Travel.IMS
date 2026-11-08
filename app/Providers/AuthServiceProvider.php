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

        // Super-Admin implicitly gets all abilities/permissions.
        // Other roles (including Admin, Manager, Sales) strictly obey their assigned Spatie permissions.
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole(['Super Admin', 'Super-Admin', 'super-admin'])) {
                    return true;
                }
            }
            if ((int) ($user->role_id ?? 0) === 1) {
                return true;
            }
            return null;
        });
    }
}
