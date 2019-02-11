<?php

namespace App\Providers;

use App\Models\CheckPoint;
use App\Models\Group;
use App\Models\Message;
use App\Models\Run;
use App\Policies\CheckPointPolicy;
use App\Policies\GroupPolicy;
use App\Policies\MessagePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\RunPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
    protected $policies = [
        Message::class => MessagePolicy::class,
        Group::class => GroupPolicy::class,
        Run::class => RunPolicy::class,
        CheckPoint::class => CheckPointPolicy::class
    ];


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
