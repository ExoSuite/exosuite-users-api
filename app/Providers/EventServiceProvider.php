<?php declare(strict_types = 1);

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

/**
 * Class EventServiceProvider
 *
 * @package App\Providers
 */

/**
 * Class EventServiceProvider
 *
 * @package App\Providers
 */
class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var string[string[]]
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }
}
