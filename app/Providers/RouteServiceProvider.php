<?php

namespace App\Providers;

use App\Enums\BindType;
use App\Models\CheckPoint;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Group;
use App\Models\Like;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PendingRequest;
use App\Models\Post;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Webpatser\Uuid\Uuid;

/**
 * Class RouteServiceProvider
 * @package App\Providers
 */
class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $_namespace = 'App\Http\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::bind(BindType::UUID, function ($uuid) {
            if (Uuid::validate($uuid)) {
                return Uuid::import($uuid);
            }
            throw new UnprocessableEntityHttpException("Bad uuid");
        });
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
        Route::bind('run_id', Run::class);
        Route::bind('checkpoint_id', CheckPoint::class);
    }


    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->_namespace)
            ->group(base_path('routes/web.php'));
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        // uncomment this line to add api/ on all api routes
        // Route::prefix('api')
        Route::middleware('api')
            ->namespace($this->_namespace)
            ->group(base_path('routes/api.php'));
    }
}
