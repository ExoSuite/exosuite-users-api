<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppendUserId
{

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

        throw new AuthenticationException();
    }
}
