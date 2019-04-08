<?php declare(strict_types = 1);

namespace App\Passport;

use Illuminate\Support\Facades\Route;

class Passport extends \Laravel\Passport\Passport
{

    /**
     * @param null $callback
     * @param array<mixed> $options
     */
    public static function routes($callback = null, array $options = []): void
    {
        if (!$callback) {
            $callback = static function ($router): void {
                $router->all();
            };
        }

        $defaultOptions = [
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];
        $options = array_merge($defaultOptions, $options);
        Route::group($options, static function ($router) use ($callback): void {
            $callback(new RouteRegistrar($router));
        });
    }
}
