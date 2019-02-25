<?php declare(strict_types = 1);

namespace App\Http\Middleware;

/**
 * Class EncryptCookies
 *
 * @package App\Http\Middleware
 */
class EncryptCookies extends \Illuminate\Cookie\Middleware\EncryptCookies
{

    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [];
}
