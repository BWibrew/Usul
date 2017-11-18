<?php

namespace App\Providers;

use GuzzleHttp\Client;
use App\ApiConnections\Wordpress;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('GuzzleHttp\Client', function () {
            return new Client;
        });
        $this->app->bind('ApiConnections\Wordpress', function () {
            return new Wordpress($this->app->make('GuzzleHttp\Client'));
        });
    }
}
