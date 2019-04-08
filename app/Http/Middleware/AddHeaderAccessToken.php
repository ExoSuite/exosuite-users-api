<?php

namespace App\Http\Middleware;

use Closure;

class AddHeaderAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $accessToken = $request->query('token');
        if (is_string($accessToken))
            $request->headers->set('Authorization', "Bearer $accessToken");
        return $next($request);
    }
}
