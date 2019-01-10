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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', 'Auth\RegisterController@register')->name('register');

    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('user')->group(function () {

        Route::prefix('me')->group(function () {

            Route::get('/', 'User\UserController@me')
                ->name('get_user');

            Route::prefix('profile')->group(function () {
                ///////////////////////////////////////////////////////////////////
                Route::post('/', 'User\UserProfileController@store')
                    ->name('post_user_profile');
                Route::patch('/', 'User\UserProfileController@update')
                    ->name('patch_user_profile');
                Route::get('/', 'User\UserProfileController@show')
                    ->name('get_user_profile');
                ///////////////////////////////////////////////////////////////////
            });
        });

        Route::get('search', 'User\UserController@search')->name('get_users');

        Route::group(['prefix' => 'follows'], function () {
            Route::post('/', 'FollowsController@store')->name('newFollow');
            Route::get('/amIFollowing', 'FollowsController@AmIFollowing')->name('amIFollowing');
            Route::get('/followers', 'FollowsController@WhoIsFollowing')->name('followers');
            Route::delete('/unFollow', 'FollowsController@delete')->name('unFollow');
        });

        Route::group(['prefix' => 'friendship'], function () {
            Route::post('/sendFriendshipRequest', 'RelationsController@sendFriendshipRequest')->name('sendRequest');
            Route::post('/accept', 'RelationsController@acceptRequest')->name('accept');
            Route::post('/decline', 'RelationsController@declineRequest')->name('decline');
            Route::get('/myFriendlist', 'RelationsController@getMyFriendships')->name('myFriendList');
            Route::get('/friendList/{target_id}', 'RelationsController@getFriendships')->name('friendList');
        });

        Route::group(['prefix' => 'pending_requests'], function () {
            Route::post('/store', 'PendingRequestController@store')->name('create');
            Route::get('/mine', 'PendingRequestController@getMyPendings')->name('getMine');
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
