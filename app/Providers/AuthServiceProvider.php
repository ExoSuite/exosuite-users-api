<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use App\Policies\GroupPolicy;
use App\Policies\MessagePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Passport;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [Message::class => MessagePolicy::class,
        Group::class => GroupPolicy::class,
        Notification::class => NotificationPolicy::class,
        User::class => UserPolicy::class];


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        if (App::environment('production')) {
            Passport::tokensExpireIn(now()->addHour());
        } else {
            Passport::tokensExpireIn(now()->addMonth());
        }
    }
}
