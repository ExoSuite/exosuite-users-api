<?php declare(strict_types = 1);

use App\Models\User;
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

    Route::prefix('preflight')->group(static function (): void {
        Route::post('/email', 'Auth\PreFlightCheckController@emailIsAlreadyRegistered')
            ->name('preflight_is_mail_available');
    });
});

Route::prefix('monitoring')->group(static function (): void {
    Route::get('/alive', 'Controller@alive');
});

Route::middleware('auth:api')->group(static function (): void {
    Route::prefix('user')->group(static function (): void {
        Route::prefix('me')->group(static function (): void {
            Route::get('/', 'User\UserController@me')
                ->name('get_user');
            Route::patch('/', 'User\UserController@update')->name('patch_user_infos');

            Route::patch('/', 'User\UserController@update')->name('patch_user');

            Route::prefix('profile')->namespace('User')->group(static function (): void {
                Route::patch('/', 'UserProfileController@update')
                    ->name('patch_user_profile');

                Route::prefix('picture')->group(static function (): void {
                    Route::post('/cover', 'UserProfilePictureController@storeCover')->name('post_picture_cover');
                    Route::post('/avatar', 'UserProfilePictureController@storeAvatar')->name('post_picture_avatar');
                });

                Route::prefix('restrictions')->group(static function (): void {
                    Route::patch('/', 'UserProfileController@updateRestrictions')
                        ->name('patch_my_profile_restrictions');

                    Route::get('/', 'UserProfileController@getProfileRestrictions')
                        ->name('get_my_profile_restrictions');
                });
            });

            Route::prefix('friendship')->group(static function (): void {
                Route::get('/', 'RelationsController@getFriendsList')->name('get_my_friendships');
                Route::delete('{friendship}', 'RelationsController@deleteFriendships')->name('delete_friendship');
            });

            Route::prefix('pending_requests')->group(static function (): void {
                Route::get('/', 'PendingRequestController@getMyPendings')->name('get_my_pending_request');
                Route::delete('/{request}', 'PendingRequestController@deletePending')->name('delete_pending_request')
                    ->middleware('can:answerRequest,request');
            });

            Route::prefix('friendship/{request}')->group(static function (): void {
                Route::post('/accept', 'RelationsController@acceptRequest')->name('post_accept_friendship_request')
                    ->middleware('can:answerRequest,request');
                Route::post('/decline', 'RelationsController@declineRequest')->name('post_decline_friendship_request')
                    ->middleware('can:answerRequest,request');
            });

            Route::prefix('follows')->group(static function (): void {
                Route::delete('{follow}', 'FollowsController@delete')->name('delete_follow');

                Route::prefix('followers')->group(static function (): void {
                    Route::get('/', 'FollowsController@getUserFollowers')->name('get_my_followers');
                    Route::get('/count', 'FollowsController@countFollowers')->name('get_my_followers_number');
                });

                Route::prefix('following')->group(static function (): void {
                    Route::get('/', 'FollowsController@getFollows')->name('get_my_follows');
                    Route::get('/count', 'FollowsController@countFollows')->name('get_my_follows_number');
                });
            });

            Route::prefix('run')->group(static function (): void {
                Route::get('/search', "Run\RunController@search")->name('search_run');

                Route::post('/', 'Run\RunController@store')
                    ->name('post_run');
                Route::patch('/{run}', 'Run\RunController@update')
                    ->name('patch_run');
                Route::get('/', 'Run\RunController@index')
                    ->name('get_my_runs');
                Route::get('/{run}', 'Run\RunController@show')
                    ->name('get_my_run_by_id');
                Route::delete('/{run}', 'Run\RunController@destroy')
                    ->name('delete_run');

                Route::prefix('/{run}/user_run')->group(static function (): void {
                    Route::post('/', 'UserRunController@store')->name('post_user_run');
                    Route::delete('/{user_run}', 'UserRunController@destroy')->name('delete_user_run');
                    Route::patch('/{user_run}', 'UserRunController@update')->name('patch_user_run');
                    Route::get('/', 'UserRunController@index')->name('get_my_user_runs');
                    Route::get('/{user_run}', 'UserRunController@show')->name('get_my_user_run_by_id');
                });

                Route::prefix('/{run}/checkpoint')->group(static function (): void {
                    Route::post('/', 'CheckPoint\CheckPointController@store')->name('post_checkpoint');
                    Route::delete('/{checkpoint}', 'CheckPoint\CheckPointController@destroy')
                        ->name('delete_checkpoint');
                    Route::put('/{checkpoint}', 'CheckPoint\CheckPointController@update')->name('put_checkpoint');
                    Route::get('/', 'CheckPoint\CheckPointController@index')->name('get_my_checkpoints');
                    Route::get('/{checkpoint}', 'CheckPoint\CheckPointController@show')
                        ->name('get_my_checkpoint_by_id');
                    Route::prefix('/{checkpoint}/time')->group(static function (): void {
                        Route::post('/', 'Time\TimeController@store')->name('post_time');
                        Route::delete('/{time}', 'Time\TimeController@destroy')->name('delete_time');
                        Route::get('/', 'Time\TimeController@index')->name('get_my_times');
                        Route::get('/{time}', 'Time\TimeController@show')->name('get_my_time_by_id');
                    });
                });
            });

            Route::prefix('dashboard')->group(static function (): void {
                Route::get('/restrictions', 'DashboardsController@getRestriction')
                    ->name('get_dashboard_restriction');
                Route::patch('/restriction', 'DashboardsController@changeRestriction')
                    ->name('patch_dashboard_restriction');
            });

            Route::get('/groups', 'User\UserController@groups')->name('get_my_groups');
        });

        Route::get('search', 'User\UserController@search')->name('get_users');

        Route::prefix('{user}')->group(static function (): void {
            Route::prefix('profile')->namespace('User')->group(static function (): void {
                Route::get('/', 'UserProfileController@show')
                    ->name('get_user_profile');

                Route::prefix('restrictions')->group(static function (): void {
                    Route::get('/', 'UserProfileController@getProfileRestrictions')
                        ->name('get_user_profile_restrictions');
                });

                Route::prefix('picture')->middleware("scope:view-picture")->group(static function (): void {
                    /*  Route::get('/', 'User\UserProfilePictureController@index')->name('get_pictures');
                    Route::post('/', 'User\UserProfilePictureController@store')->name('post_picture');*/
                    Route::get('/avatar', 'UserProfilePictureController@show')->name('get_picture_avatar');
                    Route::get('/cover', 'UserProfilePictureController@showCover')->name('get_picture_cover');
                });
            });

            //FOLLOWS-----------------------------------------------------------------------------------
            Route::prefix('follows')->group(static function (): void {
                Route::post('/', 'FollowsController@store')->name('post_follow');
                Route::get('/', 'FollowsController@amIFollowing')->name('get_am_i_following');

                Route::prefix('followers')->group(static function (): void {
                    Route::get('/', 'FollowsController@getUserFollowers')->name('get_followers');
                    Route::get('/count', 'FollowsController@countFollowers')->name('get_followers_number');
                });

                Route::prefix('following')->group(static function (): void {
                    Route::get('/', 'FollowsController@getFollows')->name('get_follows');
                    Route::get('/count', 'FollowsController@countFollows')->name('get_follows_number');
                });
            });

            //FRIENDSHIPS-----------------------------------------------------------------------------------
            Route::prefix('friendship/')->group(static function (): void {
                Route::post('/', 'RelationsController@sendFriendshipRequest')->name('post_friendship_request');
                Route::get('/', 'RelationsController@getFriendsList')->name('get_friendships');
            });

            //DASHBOARDS-----------------------------------------------------------------------------------------
            Route::prefix('dashboard')->group(static function (): void {
                Route::get('/', 'DashboardsController@getDashboardId')
                    ->name('get_dashboard_id');

                //POSTS-----------------------------------------------------------------------------------------
                Route::prefix('/posts')->group(static function (): void {
                    Route::post('/', 'PostsController@store')->name('post_Post')
                        ->middleware('can:createPost,user');

                    Route::get('/', 'PostsController@getPostsFromDashboard')->name('get_Posts_from_dashboard')
                        ->middleware('can:getPost,user');

                    Route::prefix('{post}')->group(static function (): void {
                        Route::patch('/', 'PostsController@update')->name('patch_Post')
                            ->middleware('can:updatePost,post');
                        Route::delete('/', 'PostsController@delete')->name('delete_Post')
                            ->middleware('can:deletePost,post,user');

                        //LIKES From Posts-------------------------------------------------------------------------------------------------
                        Route::prefix('/likes')->group(static function (): void {
                            Route::post('/', 'LikesController@store')->name('post_like_for_Post');
                            Route::delete('/', 'LikesController@delete')->name('delete_like_for_Post');
                            Route::get('/', 'LikesController@getLikesFromID')->name('get_likes_from_Post');
                        });

                        //COMMENTARIES-----------------------------------------------------------------------------------------
                        Route::prefix('/commentaries')->group(static function (): void {
                            Route::post('/', 'CommentaryController@store')->name('post_commentary')
                                ->middleware('can:create,post,user');
                            Route::patch('/{commentary}', 'CommentaryController@updateComm')->name('patch_commentary')
                                ->middleware('can:updateCommentary,commentary');
                            Route::get('/', 'CommentaryController@getCommsFromPost')
                                ->name('get_commentaries_by_post_id')
                                ->middleware('can:index,post,user');
                            Route::delete('/{commentary}', 'CommentaryController@deleteComm')
                                ->name('delete_commentary')
                                ->middleware('can:deleteCommentary,commentary,post');

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

            Route::prefix('run')->group(static function (): void {
                Route::get('/search', "Run\RunController@search")->name('search_run');
                Route::get('/', 'Run\RunController@index')
                    ->name('get_runs');
                Route::get('/{run}', 'Run\RunController@show')
                    ->name('get_run_by_id');
                Route::get('search', 'Run\RunController@search')->name("search_run");
                Route::prefix('share')->group(static function (): void {
                    Route::post('/', 'Run\ShareRunController@store')
                        ->name('post_share_run');
                    Route::get('/', 'Run\ShareRunController@index')
                        ->name('get_share_run');
                    Route::get('/id/{uuid}', 'Run\ShareRunController@show')
                        ->name('get_share_run_by_id');
                });
                Route::prefix('{run}')->group(static function (): void {
                    Route::prefix('/likes')->group(static function (): void {
                        Route::post('/', 'LikesController@storeRun')->name('post_like_for_run');
                        Route::delete('/', 'LikesController@deleteRun')->name('delete_like_for_run');
                        Route::get('/', 'LikesController@getLikesFromRun')->name('get_likes_from_run');
                    });

                    Route::prefix('/user_run')->group(static function (): void {
                        Route::get('/', 'UserRunController@index')->name('get_user_runs');
                        Route::get('/{user_run}', 'UserRunController@show')->name('get_user_run_by_id');
                    });

                    Route::prefix('/checkpoint')->group(static function (): void {
                        Route::get('/', 'CheckPoint\CheckPointController@index')->name('get_checkpoints');
                        Route::get('/{checkpoint}', 'CheckPoint\CheckPointController@show')
                            ->name('get_checkpoint_by_id');
                        Route::prefix('/{checkpoint}/time')->group(static function (): void {
                            Route::get('/', 'Time\TimeController@index')->name('get_times');
                            Route::get('/{time}', 'Time\TimeController@show')->name('get_time_by_id');
                        });
                    });
                });
            });
        });
    });

    Route::prefix('notification')->group(static function (): void {
        Route::patch('/{notification?}', 'NotificationController@update')->name('patch_notification');
        Route::get('/', 'NotificationController@index')->name('get_notification');
        Route::delete('/{notification?}', 'NotificationController@destroy')->name('delete_notification');
    });

    Route::prefix('group')->group(static function (): void {
        Route::post('/', 'GroupController@store')->name('post_group');
        Route::patch('/{group}', 'GroupController@update')->name('patch_group')
            ->middleware('can:update,group');
        Route::get('/{group}', 'GroupController@show')->name('get_group');
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

if (!App::environment('production')) {
    Route::get('staging/client', 'StagingController@get')->name('staging-client');
}

if (App::environment('local')) {
    Route::get('test', static function () {
        for ($i = 0; $i < 1000; $i++) {
            factory(User::class)->create();
        }
    });
}
