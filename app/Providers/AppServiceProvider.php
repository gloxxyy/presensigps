<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Paksakan URL dasar memakai APP_URL dari .env untuk menimpa isu cache localhost
        if (env('APP_URL') !== 'http://localhost') {
            URL::forceRootUrl(env('APP_URL'));
        }
    }
}