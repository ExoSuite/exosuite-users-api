<?php


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

Route::prefix('auth')->group(function () {
    Route::post('/register', 'Auth\RegisterController@register')->name('register');

    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::prefix('monitoring')->group(function () {
   Route::get('/alive', "Controller@alive");
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('user')->group(function () {

        Route::prefix('me')->group(function () {

            Route::get('/', 'User\UserController@me')
                ->name('get_user');

            Route::patch('/', "User\UserController@update")->name("patch_user");

            Route::prefix('profile')->group(function () {
                Route::patch('/', 'User\UserProfileController@update')
                    ->name('patch_user_profile');
            });
        });

        Route::prefix('{user}/profile')->group(function() {
            Route::get('/', 'User\UserProfileController@show')
                ->name('get_user_profile');
        });

        Route::get('search', 'User\UserController@search')->name('get_users');

        //FOLLOWS-----------------------------------------------------------------------------------
        Route::prefix('follows')->group(function () {
            Route::post('/', 'FollowsController@store')->name('follow');
            Route::get('/amIFollowing', 'FollowsController@AmIFollowing')->name('amIFollowing');
            Route::get('/followers', 'FollowsController@WhoIsFollowing')->name('followers');
            Route::delete('/unFollow', 'FollowsController@delete')->name('unfollow');
        });


        //FRIENDSHIPS-----------------------------------------------------------------------------------
        Route::prefix('friendship')->group(function () {
            Route::post('/sendFriendshipRequest', 'RelationsController@sendFriendshipRequest')->name('sendFriendshipRequest');
            Route::post('/accept', 'RelationsController@acceptRequest')->name('acceptFriendship');
            Route::post('/decline', 'RelationsController@declineRequest')->name('declineFriendship');
            Route::get('/myFriendlist', 'RelationsController@getMyFriendships')->name('myFriendList');
            Route::get('/friendList/{target_id}', 'RelationsController@getFriendships')->name('friendList');
        });

        //PENDING REQUESTS-----------------------------------------------------------------------------------
        Route::prefix('pending_requests')->group(function () {
            Route::post('/store', 'PendingRequestController@store')->name('create');
            Route::get('/mine', 'PendingRequestController@getMyPendings')->name('getMine');
        });

        //DASHBOARDS-----------------------------------------------------------------------------------------
        Route::prefix('dashboard')->group(function () {
            Route::get('/restriction', 'DashboardsController@getRestriction')->name('getRestriction');
            Route::patch('/restriction', 'DashboardsController@changeRestriction')->name('changeRestriction');
            Route::get('/dashboardId', 'DashboardsController@getDashboardId')->name('getSomeoneDashboardId');
        });

        //POSTS-----------------------------------------------------------------------------------------
        Route::prefix('posts')->group(function () {
            Route::post('/', 'PostsController@store')->name('storePost');
            Route::patch('/', 'PostsController@update')->name('patchPost');
            Route::get('/', 'PostsController@getPostsFromDashboard')->name('getPosts');
            Route::delete('/', 'PostsController@delete')->name('deletePost');

        });

        //COMMENTARIES-----------------------------------------------------------------------------------------
        Route::prefix('commentary')->group(function () {
            Route::post('/', 'CommsController@store')->name('storeCommentary');
            Route::patch('/', 'CommsController@updateComm')->name('updateCommentary');
            Route::get('/', 'CommsController@getCommsFromPost')->name('getComms');
            Route::delete('/', 'CommsController@deleteComm')->name('deleteCommentary');
        });

        //LIKES---------------------------------------------------------------------------------------------------
        Route::prefix('likes')->group(function () {
           Route::post('/', 'LikesController@store')->name('like');
           Route::delete('/', 'LikesController@delete')->name('unlike');
           Route::get('/from/entity', 'LikesController@getLikesFromID')->name('getlikesId');
           Route::get('/from/someone', 'LikesController@getLikesFromLiker')->name('getlikesLiker');
        });
    });

    Route::prefix('run')->group(function () {
        ///////////////////////////////////////////////////////////////////
        Route::post('/', 'Run\RunController@store')
            ->name('post_run');

        Route::patch('/{uuid}', 'Run\RunController@update')
            ->name('patch_run');

        Route::get('/id/{uuid}', 'Run\RunController@show')
            ->name('get_run_by_id');

        Route::get('/', 'Run\RunController@index')
            ->name('get_run');
        ///////////////////////////////////////////////////////////////////
        Route::prefix('share')->group(function () {
            Route::post('/', 'Run\ShareRunController@store')
                ->name('post_share_run');
            Route::get('/', 'Run\ShareRunController@index')
                ->name('get_share_run');
            Route::get('/id/{uuid}', 'Run\ShareRunController@show')
                ->name('get_share_run_by_id');
        });
    });

});

if (!\Illuminate\Support\Facades\App::environment("production")) {
    Route::get('staging/client', 'StagingController@get')->name('staging-client');
}

if (\Illuminate\Support\Facades\App::environment("local")) {
    Route::get('test', function () {
        \Illuminate\Support\Facades\Notification::send(
            App\Models\User::all(),
            new \App\Notifications\FollowNotification()
        );
        return ["SENT!"];
    });
}
