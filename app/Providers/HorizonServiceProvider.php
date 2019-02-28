<?php declare(strict_types = 1);

namespace App\Providers;

use App\Enums\Roles;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use function app;

/**
 * Class HorizonServiceProvider
 *
 * @package \App\Providers
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', static function ($user) {
            if (app()->runningUnitTests()) {
                return true;
            }

            return $user->inRole(Roles::ADMINISTRATOR);
        });
    }
}
