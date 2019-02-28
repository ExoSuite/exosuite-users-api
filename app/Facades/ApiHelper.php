<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:31
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use function config;

/**
 * Class ApiHelper
 *
 * @package App\Facades
 * @method \App\Services\OAuth OAuth()
 * @method \Illuminate\Http\RedirectResponse redirectToLogin($redirectUrl = null)
 */
class ApiHelper extends Facade
{
    public static function isStaging(): bool
    {
        return config("app.env") === 'staging';
    }

    public static function isProduction(): bool
    {
        return config("app.env") === 'production';
    }

    public static function isLocal(): bool
    {
        return config("app.env") === 'local';
    }

    protected static function getFacadeAccessor(): string
    {
        return 'ApiHelper';
    }
}
