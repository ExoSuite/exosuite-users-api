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

        Route::prefix('{user}/profile')->group(function () {
            Route::get('/', 'User\UserProfileController@show')
                ->name('get_user_profile');
        });

        Route::get('search', 'User\UserController@search')->name('get_users');
    });

    Route::prefix('notification')->group(function () {
        Route::patch('/{notification?}', 'NotificationController@update')->name('patch_notification');
        Route::get('/', 'NotificationController@index')->name('get_notification');
        Route::delete('/{notification?}', 'NotificationController@destroy')->name('delete_notification');
    });

    Route::prefix('group')->group(function () {
        Route::post('/', 'GroupController@store')->name('post_group');
        Route::patch('/{group}', 'GroupController@update')->name('patch_group');
        Route::get('/{group}', 'GroupController@index')->name('get_group');
        Route::delete('/{group}', 'GroupController@destroy')->name('delete_group');
        Route::prefix('/{group}/message')->group(function () {
            Route::post('/', 'MessageController@store')->name('post_message')->middleware('can:createGroupMessage,group');
            Route::patch('/{message}', 'MessageController@update')->name('patch_message')->middleware('can:update,message');
            Route::get('/', 'MessageController@index')->name('get_message')->middleware('can:viewGroupMessages,group');
            Route::delete('/{message}', 'MessageController@destroy')->name('delete_message')->middleware('can:delete,message');
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
        Route::prefix('{run_id}/checkpoint')->group(function () {
            Route::prefix('{checkpoint_id}/time')->group(function () {
            });
        });
    });
});

if (!\Illuminate\Support\Facades\App::environment("production")) {
    Route::get('staging/client', 'StagingController@get');
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
