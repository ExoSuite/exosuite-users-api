<?php

namespace App\Providers;

use App\Services\ApiHelper;
use App\Services\InternalRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class FacadesServiceProvider
 * @package App\Providers
 */
class FacadesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::singleton(
            'InternalRequest',
            function ($app) {
                return new InternalRequest($app);
            }
        );

        App::singleton(
            'ApiHelper',
            function () {
                return new ApiHelper();
            }
        );
    }
}
