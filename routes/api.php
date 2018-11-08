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
    });

    Route::group(['prefix' => 'run'], function () {
        Route::post('/', 'RunController@store');
        Route::get('/id/{uuid}', 'RunController@show');
        Route::get('/', 'RunController@index');
    });
});

if (!\Illuminate\Support\Facades\App::environment("production")) {
    Route::get('staging/client', 'StagingController@get');
}

/*Route::get('test', function () {
    for ($i = 0; $i < 10000; $i++) {
        \Illuminate\Support\Facades\Notification::send(
            App\Models\User::all(),
            new \App\Notifications\FollowNotification()
        );
    }
    return ["SENT!"];
});*/
