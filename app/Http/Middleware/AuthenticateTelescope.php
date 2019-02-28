<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\AdministratorServices;
use Closure;
use Illuminate\Http\Request;
use Laravel\Telescope\Telescope;
use function is_bool;

/**
 * Class AuthenticateTelescope
 *
 * @package App\Http\Middleware
 */
class AuthenticateTelescope
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var boolean $authenticated */
        $authenticated = AdministratorServices::handleAuth($request);

        // check if HorizonService::handleAuth has return a boolean
        if (is_bool($authenticated)) {
            Telescope::auth(static function ($request) use ($authenticated) {
                return $authenticated;
            });
        }

        return $next($request);
    }
}
