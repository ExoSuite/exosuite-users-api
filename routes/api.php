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

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'user'], function () {

        Route::group(['prefix' => 'me'], function () {

            Route::get('/', 'UserController@me')->name('get_user');

            Route::group(['prefix' => 'profile'], function () {
                Route::post('/', 'UserProfileController@store')
                    ->name('post_user_profile');

                Route::patch('/', 'UserProfileController@update')
                    ->name('patch_user_profile');

                Route::get('/', 'UserProfileController@show')->name('get_user_profile');
            });
        });

        Route::get('search', 'UserController@search')->name('get_users');

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
});

if (!\Illuminate\Support\Facades\App::environment("production")) {
    Route::get('staging/R', 'StagingController@get')->name('staging-client');
}

Route::get('test', function () {
    for ($i = 0; $i < 10000; $i++) {
        \Illuminate\Support\Facades\Notification::send(
            App\Models\User::all(),
            new \App\Notifications\FollowNotification()
        );
    }
    return ["SENT!"];
});
