<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\AdministratorServices;
use Closure;
use Illuminate\Http\Request;
use Laravel\Horizon\Horizon;
use function is_bool;

/**
 * Class AuthenticateHorizon
 *
 * @package App\Http\Middleware
 */
class AuthenticateHorizon
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var mixed $authenticated */
        $authenticated = AdministratorServices::handleAuth($request);

        // check if HorizonService::handleAuth has return a boolean
        if (is_bool($authenticated)) {
            Horizon::auth(static function ($request) use ($authenticated) {
                return $authenticated;
            });
        } else {
            return $authenticated;
        }

        return $next($request);
    }
}
