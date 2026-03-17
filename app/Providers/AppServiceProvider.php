<?php

namespace App\Providers;

use App\Models\AppSetting;
use Throwable;
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
        try {
            if (!Schema::hasTable('app_settings')) {
                return;
            }
        } catch (Throwable $exception) {
            return;
        }

        config([
            'app.registration_enabled' => AppSetting::getBoolean('registration_enabled', config('app.registration_enabled')),
        ]);
    }
}
