<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dnetix\Redirection\PlacetoPay;

class GatewaySeriveProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PlacetoPay::class, function ($app) {
            return new PlacetoPay([
                'login' => env('PLACE_TO_PAY_LOGIN'),
                'tranKey' => env('PLACE_TO_TRAN_KEY'),
                'url' => env('PLACE_TO_TRAN_URL'),
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
