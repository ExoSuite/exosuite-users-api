<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class AdministratorServices
 *
 * @package App\Services
 * @method boolean|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse handleAuth($request)
 */
class AdministratorServices extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'AdministratorServices';
    }
}
