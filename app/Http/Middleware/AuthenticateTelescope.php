<?php

namespace App\Http\Middleware;

use App\Facades\AdministratorServices;
use Closure;
use Laravel\Telescope\Telescope;

/**
 * Class AuthenticateTelescope
 * @package App\Http\Middleware
 */
class AuthenticateTelescope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var boolean $authenticated */
        $authenticated = AdministratorServices::handleAuth($request);

        // check if HorizonService::handleAuth has return a boolean
        if (is_bool($authenticated)) {
            Telescope::auth(function ($request) use ($authenticated) {
                return $authenticated;
            });
        }

        return $next($request);
    }
}
