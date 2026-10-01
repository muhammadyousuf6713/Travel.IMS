<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Reviews module: automatic review sync when an inquiry becomes Completed (switch in Integration Settings)
        \App\inquiry::observe(\App\Observers\InquiryReviewObserver::class);

        try {
            if (Schema::hasTable('organization_settings')) {
                $orgSetting = \App\Models\OrganizationSetting::first();
                if (!$orgSetting) {
                    $orgSetting = \App\Models\OrganizationSetting::create([
                        'name' => 'Flugzeit Aviation',
                        'theme_color' => '#cb0c9f', // Soft UI primary pink/purple default
                        'logo' => '',
                        'login_bg' => ''
                    ]);
                }
                \Illuminate\Support\Facades\View::share('orgSetting', $orgSetting);
            }
        } catch (\Exception $e) {
            // Ignore during migrations or clear cache
        }
    }
}
