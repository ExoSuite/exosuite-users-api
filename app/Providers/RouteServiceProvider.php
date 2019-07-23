<?php declare(strict_types = 1);

namespace App\Providers;

use App\Enums\BindType;
use App\Models\CheckPoint;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Group;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PendingRequest;
use App\Models\Post;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use App\Models\UserRun;
use Illuminate\Support\Facades\Route;
use function base_path;

/**
 * Class RouteServiceProvider
 *
 * @package App\Providers
 */
class RouteServiceProvider extends \Illuminate\Foundation\Support\Providers\RouteServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Route::model(BindType::GROUP, Group::class);
        Route::model(BindType::MESSAGE, Message::class);
        Route::model(BindType::NOTIFICATION, Notification::class);
        Route::model(BindType::USER, User::class);
        Route::model(BindType::PENDING_REQUEST, PendingRequest::class);
        Route::model(BindType::FRIENDSHIP, Friendship::class);
        Route::model(BindType::FOLLOW, Follow::class);
        Route::model(BindType::DASHBOARD, Dashboard::class);
        Route::model(BindType::POST, Post::class);
        Route::model(BindType::COMMENTARY, Commentary::class);
        Route::model(BindType::RUN, Run::class);
        Route::model(BindType::CHECKPOINT, CheckPoint::class);
        Route::model(BindType::TIME, Time::class);
        Route::model(BindType::USER_RUN, UserRun::class);
    }


    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        // uncomment this line to add api/ on all api routes
        // Route::prefix('api')
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}
