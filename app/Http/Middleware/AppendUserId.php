<?php declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Http\Middleware\AuthenticationException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AppendUserId
 *
 * @package App\Http\Middleware
 */
class AppendUserId
{

    /** @var string */
    public static $key = "requester_user_id";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()) {
            $append = [self::$key => Auth::id()];

            if ($request->isMethod(Request::METHOD_POST)
                || $request->isMethod(Request::METHOD_PATCH)
                || $request->isMethod(Request::METHOD_PUT)
            ) {
                $request->request->add($append);
            } else {
                $request->query->add($append);
            }

            return $next($request);
        }

        throw new AuthenticationException;
    }
}
