<?php declare(strict_types = 1);

namespace App\Providers;

use App\Enums\Roles;
use App\Facades\ApiHelper;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * Class TelescopeServiceProvider
 *
 * @package App\Providers
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(static function (IncomingEntry $entry) {
            if (ApiHelper::isLocal() || ApiHelper::isStaging()) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });

        Telescope::tag(static function (IncomingEntry $entry) {
            if ($entry->type === 'request') {
                return ['status:' . $entry->content['response_status']];
            }

            return [];
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->isLocal()) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', static function ($user) {
            return $user->inRole(Roles::ADMINISTRATOR);
        });
    }
}
