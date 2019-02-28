<?php declare(strict_types = 1);

namespace App\Providers;

use App\Facades\ApiHelper;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Resource::withoutWrapping();
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Passport::ignoreMigrations();

        if (!$this->app->isLocal() && !ApiHelper::isStaging()) {
            return;
        }

        $this->app->register(TelescopeServiceProvider::class);
    }
}
