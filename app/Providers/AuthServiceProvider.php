<?php declare(strict_types = 1);

namespace App\Providers;

use App\Models\Commentary;
use App\Models\Group;
use App\Models\Message;
use App\Models\PendingRequest;
use App\Models\Post;
use App\Models\User;
use App\Policies\CommentaryPolicy;
use App\Policies\GroupPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PendingRequestsPolicy;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Passport;
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

        if (App::environment('production')) {
            Passport::tokensExpireIn(now()->addHour());
        } else {
            Passport::tokensExpireIn(now()->addMonth());
        }
    }
}
