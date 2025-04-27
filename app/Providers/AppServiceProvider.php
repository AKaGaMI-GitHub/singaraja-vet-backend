<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
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

        if (env('APP_ENV') === "local") {
            if (App::runningInConsole() && php_sapi_name() === 'cli-server') {
                Artisan::call('queue:work');
            }
        }

        if (env('APP_ENV') !== "local") {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
