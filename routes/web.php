<?php declare(strict_types = 1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('auth')->group(static function (): void {
    Route::prefix('password')->group(static function (): void {
        Route::prefix('reset')->group(static function (): void {
            Route::get('/', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
            Route::get('/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

            Route::post('/', 'Auth\ResetPasswordController@reset')->name('password.update');
            Route::post('/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        });

        Route::get("successful", "Auth\ResetPasswordController@successful")->name('password.success');
    });
});

Route::get('/', "RedirectToWebsiteController@redirect");
