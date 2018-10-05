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

            Route::get('/', 'UserController@me')->name('personal_user_infos');

            Route::group(['prefix' => 'profile'], function () {
                Route::post('/', 'UserProfileController@store')
                    ->name('user_profile_create')
                    ->middleware('append_user_id');

                Route::patch('/', 'UserProfileController@update')
                    ->name('user_profile_update')
                    ->middleware('append_user_id');

                Route::get('/', 'UserProfileController@show')->name('user_profile_get');
            });
        });

        Route::get('search', 'UserController@search')->name('user_search');
    });
});

if (\Illuminate\Support\Facades\App::environment("staging")) {
    Route::get('/client/default');
}
