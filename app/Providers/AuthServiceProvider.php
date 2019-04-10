<?php declare(strict_types = 1);

namespace App\Providers;

use App\Enums\TokenScope;
use App\Facades\ApiHelper;
use App\Models\CheckPoint;
use App\Models\Commentary;
use App\Models\Group;
use App\Models\Message;
use App\Models\PendingRequest;
use App\Models\Post;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use App\Passport\Passport;
use App\Policies\CheckPointPolicy;
use App\Policies\CommentaryPolicy;
use App\Policies\GroupPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PendingRequestsPolicy;
use App\Policies\PostPolicy;
use App\Policies\RunPolicy;
use App\Policies\TimePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\App;
use function now;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
class AuthServiceProvider extends \Illuminate\Foundation\Support\Providers\AuthServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var string[]
     */
    protected $policies = [
        Message::class => MessagePolicy::class,
        Group::class => GroupPolicy::class,
        CheckPoint::class => CheckPointPolicy::class,
        Time::class => TimePolicy::class,
        Run::class => RunPolicy::class,
        Post::class => PostPolicy::class,
        Commentary::class => CommentaryPolicy::class,
        PendingRequest::class => PendingRequestsPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensCan([
            TokenScope::VIEW_PICTURE => 'ability to access picture resources',
            TokenScope::CONNECT_IO => "ability to connect on io server.",
            TokenScope::GROUP => "ability to interact with group resource",
            TokenScope::MESSAGE => "ability to interact with message resource",
        ]);

        if (ApiHelper::isLocal()) {
            Passport::personalAccessClientId(1);
        } else if (ApiHelper::isStaging()) {
            Passport::personalAccessClientId(25);
        } else {
            Passport::personalAccessClientId(3);
        }

        if (ApiHelper::isStaging()) {
            Passport::tokensExpireIn(now()->addMinutes(5));
        } else if (ApiHelper::isProduction()) {
            Passport::tokensExpireIn(now()->addMonth());
        } else {
            Passport::tokensExpireIn(now()->addHour());
        }
    }
}
