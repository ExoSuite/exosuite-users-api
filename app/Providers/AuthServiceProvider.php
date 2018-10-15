<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Passport;
use Laravel\Horizon\Horizon;

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
    protected $policies = ['App\Model' => 'App\Policies\ModelPolicy'];


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
            Passport::tokensExpireIn(now()->addMinutes(5));
        }

        Horizon::auth(function ($request) {
            return true;
        });
    }
}
