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
        Route::delete('{uuid}', 'Run\RunController@delete')
            ->name('delete_run');
        ///////////////////////////////////////////////////////////////////
        Route::prefix('share')->group(function () {
            Route::post('/', 'Run\ShareRunController@store')
                ->name('post_share_run');
            Route::get('/', 'Run\ShareRunController@index')
                ->name('get_share_run');
            Route::get('/id/{uuid}', 'Run\ShareRunController@show')
                ->name('get_share_run_by_id');
        });
        ///////////////////////////////////////////////////////////////////
        Route::prefix('{run_id}/checkpoint')->group(function() {
                Route::prefix('{checkpoint_id}/time')->group(function() {
                });
        });
    });
});

if (!\Illuminate\Support\Facades\App::environment("production")) {
    Route::get('staging/client', 'StagingController@get');
}

Route::get('test', function () {
    \Illuminate\Support\Facades\Notification::send(
        App\Models\User::all(),
        new \App\Notifications\FollowNotification()
    );
    return ["SENT!"];
});
