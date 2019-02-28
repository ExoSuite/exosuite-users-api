<?php declare(strict_types = 1);

namespace App\Providers;

use App\Services\AdministratorServices;
use App\Services\ApiHelper;
use App\Services\InternalRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class FacadesServiceProvider
 *
 * @package App\Providers
 */
class FacadesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        App::singleton('InternalRequest', static function ($app) {
            return new InternalRequest($app);
        });

        App::singleton('ApiHelper', static function () {
            return new ApiHelper;
        });

        App::singleton('AdministratorServices', static function () {
            return new AdministratorServices;
        });
    }
}
