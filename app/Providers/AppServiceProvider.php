<?php

namespace App\Providers;

use App\Contracts\SpotifyApiContract;
use App\Services\SpotifyApiService;
use Illuminate\Database\Schema\Builder;
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
        Builder::defaultMorphKeyType('uuid');

        $this->app->singleton(SpotifyApiContract::class, function ($app) {
            return new SpotifyApiService(
                rtrim($app['config']['services.spotify.endpoint'], '/').'/',
                $app['config']['services.spotify.client_id'],
                $app['config']['services.spotify.client_secret']
            );
        });
    }
}
