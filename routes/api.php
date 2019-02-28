<?php declare(strict_types = 1);

use App\Notifications\FollowNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(static function (): void {
    Route::post('/register', 'Auth\RegisterController@register')->name('register');

    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::prefix('monitoring')->group(static function (): void {
    Route::get('/alive', 'Controller@alive');
});

Route::middleware('auth:api')->group(static function (): void {
    Route::prefix('user')->group(static function (): void {
        Route::prefix('me')->group(static function (): void {
            Route::get('/', 'User\UserController@me')
                ->name('get_user');

            Route::patch('/', 'User\UserController@update')->name('patch_user');

            Route::prefix('profile')->group(static function (): void {
                Route::patch('/', 'User\UserProfileController@update')
                    ->name('patch_user_profile');
            });

            Route::prefix('friendship')->group(static function (): void {
                Route::get('/', 'RelationsController@getMyFriendships')->name('get_my_friendships');
            });

            Route::prefix('pending_requests')->group(static function (): void {
                Route::get('/', 'PendingRequestController@getMyPendings')->name('get_my_pending_request');
                Route::delete('/{request}', 'PendingRequestController@deletePending')->name('delete_pending_request');
            });

            Route::prefix('friendship/{request}')->group(static function (): void {
                Route::post('/accept', 'RelationsController@acceptRequest')->name('post_accept_friendship_request');
                Route::post('/decline', 'RelationsController@declineRequest')->name('post_decline_friendship_request');
            });
        });

        Route::get('search', 'User\UserController@search')->name('get_users');

        Route::prefix('{user}')->group(static function (): void {
            Route::prefix('profile')->group(static function (): void {
                Route::get('/', 'User\UserProfileController@show')
                    ->name('get_user_profile');
            });

            Route::prefix('picture')->group(static function (): void {
                /*  Route::get('/', 'User\UserProfilePictureController@index')->name('get_pictures');
                Route::post('/', 'User\UserProfilePictureController@store')->name('post_picture');*/
                Route::post('/avatar', 'User\UserProfilePictureController@storeAvatar')->name('post_picture_avatar');
                Route::get('/avatar', 'User\UserProfilePictureController@show')->name('get_picture_avatar');
                Route::post('/cover', 'User\UserProfilePictureController@storeCover')->name('post_picture_cover');
                Route::get('/cover', 'User\UserProfilePictureController@showCover')->name('get_picture_cover');
            });

            //FOLLOWS-----------------------------------------------------------------------------------
            Route::prefix('follows')->group(static function (): void {
                Route::post('/', 'FollowsController@store')->name('post_follow');
                Route::get('/followers', 'FollowsController@whoIsFollowing')->name('get_followers');
                Route::get('/', 'FollowsController@amIFollowing')->name('get_am_i_following');
                Route::delete('/', 'FollowsController@delete')->name('delete_follow');
            });

            //FRIENDSHIPS-----------------------------------------------------------------------------------
            Route::prefix('friendship')->group(static function (): void {
                Route::post('/', 'RelationsController@sendFriendshipRequest')->name('post_friendship_request');

                Route::get('/', 'RelationsController@getFriendships')->name('get_friendships');
                Route::delete('/', 'RelationsController@deleteFriendships')->name('delete_friendship');
            });

            //DASHBOARDS-----------------------------------------------------------------------------------------
            Route::prefix('dashboard')->group(static function (): void {
                Route::get('/restriction', 'DashboardsController@getRestriction')
                    ->name('get_dashboard_restriction');
                Route::patch('/restriction', 'DashboardsController@changeRestriction')
                    ->name('patch_dashboard_restriction');
                Route::get('/', 'DashboardsController@getDashboardId')
                    ->name('get_dashboard_id');

                //POSTS-----------------------------------------------------------------------------------------
                Route::prefix('{dashboard}/post')->group(static function (): void {
                    Route::post('/', 'PostsController@store')->name('post_Post');

                    Route::get('/', 'PostsController@getPostsFromDashboard')->name('get_Posts_by_dashboard_id');

                    Route::prefix('{post}')->group(static function (): void {
                        Route::patch('/', 'PostsController@update')->name('patch_Post');
                        Route::delete('/', 'PostsController@delete')->name('delete_Post');

                        //LIKES From Posts---------------------------------------------------------------------------------------------------
                        Route::prefix('/likes')->group(static function (): void {
                            Route::post('/', 'LikesController@store')->name('post_like_for_Post');
                            Route::delete('/', 'LikesController@delete')->name('delete_like_for_Post');
                            Route::get('/', 'LikesController@getLikesFromID')->name('get_likes_from_Post');
                        });

                        //COMMENTARIES-----------------------------------------------------------------------------------------

                        Route::prefix('/commentary')->group(static function (): void {
                            Route::post('/', 'CommentaryController@store')->name('post_commentary');
                            Route::patch('/{commentary}', 'CommentaryController@updateComm')->name('patch_commentary');
                            Route::get('/', 'CommentaryController@getCommsFromPost')
                                ->name('get_commentaries_by_post_id');
                            Route::delete('/{commentary}', 'CommentaryController@deleteComm')
                                ->name('delete_commentary');

                            //LIKES From Commentaries---------------------------------------------------------------------------------------------------
                            Route::prefix('{commentary}')->group(static function (): void {
                                Route::prefix('/likes')->group(static function (): void {
                                    Route::post('/', 'LikesController@store')
                                        ->name('post_like_for_commentary');
                                    Route::delete('/', 'LikesController@delete')
                                        ->name('delete_like_for_commentary');
                                    Route::get('/', 'LikesController@getLikesFromID')
                                        ->name('get_likes_from_commentary');
                                });
                            });
                        });
                    });
                });
            });

            Route::get('/', 'LikesController@getLikesFromLiker')->name('get_likes_from_liker');

            //PENDING REQUESTS-----------------------------------------------------------------------------------
            Route::prefix('pending_requests')->group(static function (): void {
                Route::post('/', 'PendingRequestController@store')->name('post_pending_request');
            });
        });
    });
    Route::get('search', 'User\UserController@search')->name('get_users');

    Route::prefix('notification')->group(static function (): void {
        Route::patch('/{notification?}', 'NotificationController@update')->name('patch_notification');
        Route::get('/', 'NotificationController@index')->name('get_notification');
        Route::delete('/{notification?}', 'NotificationController@destroy')->name('delete_notification');
    });

    Route::prefix('group')->group(static function (): void {
        Route::post('/', 'GroupController@store')->name('post_group');
        Route::patch('/{group}', 'GroupController@update')->name('patch_group')
            ->middleware('can:update,group');
        Route::get('/{group}', 'GroupController@index')->name('get_group');
        Route::delete('/{group}', 'GroupController@destroy')
            ->name('delete_group')
            ->middleware('can:delete,group');
        Route::prefix('/{group}/message')->group(static function (): void {
            Route::post('/', 'MessageController@store')
                ->name('post_message')
                ->middleware('can:createGroupMessage,group');
            Route::patch('/{message}', 'MessageController@update')
                ->name('patch_message')
                ->middleware('can:update,message');

            Route::get('/', 'MessageController@index')
                ->name('get_message')
                ->middleware('can:viewGroupMessages,group');

            Route::delete('/{message}', 'MessageController@destroy')
                ->name('delete_message')
                ->middleware('can:delete,message');
        });
    });
});


Route::prefix('run')->group(static function (): void {
    ///////////////////////////////////////////////////////////////////
    Route::post('/', 'Run\RunController@store')
        ->name('post_run');

    Route::patch('/{uuid}', 'Run\RunController@update')
        ->name('patch_run');

    Route::get('/id/{uuid}', 'Run\RunController@show')
        ->name('get_run_by_id');

    Route::get('/', 'Run\RunController@index')
        ->name('get_run');
    Route::delete('{uuid}', 'Run\RunController@delete')
        ->name('delete_run');
    ///////////////////////////////////////////////////////////////////
    Route::prefix('share')->group(static function (): void {
        Route::post('/', 'Run\ShareRunController@store')
            ->name('post_share_run');
        Route::get('/', 'Run\ShareRunController@index')
            ->name('get_share_run');
        Route::get('/id/{uuid}', 'Run\ShareRunController@show')
            ->name('get_share_run_by_id');
    });
    ///////////////////////////////////////////////////////////////////
    Route::prefix('{run}')->group(static function (): void {
        //LIKES From Runs-----------------------------------------------------------------
        Route::prefix('/likes')->group(static function (): void {
            Route::post('/', 'LikesController@storeRun')->name('post_like_for_run');
            Route::delete('/', 'LikesController@deleteRun')->name('delete_like_for_run');
            Route::get('/', 'LikesController@getLikesFromRun')->name('get_likes_from_run');
        });

        Route::prefix('checkpoint')->group(static function (): void {
            Route::prefix('{checkpoint_id}/time')->group(static function (): void {
            });
        });
    });
});

if (!App::environment('production')) {
    Route::get('staging/client', 'StagingController@get')->name('staging-client');
}

if (App::environment('local')) {
    Route::get('test', static function () {
        Notification::send(
            App\Models\User::all(),
            new FollowNotification
        );

        return ['SENT!'];
    });
}
