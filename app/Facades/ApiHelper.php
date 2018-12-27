<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:31
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\App;


/**
 * Class ApiHelper
 * @package App\Facades
 * @method \App\Services\OAuth OAuth()
 * @method \Illuminate\Http\RedirectResponse redirectToLogin($redirectUrl = null)
 */
class ApiHelper extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ApiHelper';
    }

    /**
     * @return bool
     */
    public static function isStaging(): bool
    {
        return App::environment() === 'staging';
    }

    /**
     * @return bool
     */
    public static function isProduction(): bool
    {
        return App::environment() === 'production';
    }
}
