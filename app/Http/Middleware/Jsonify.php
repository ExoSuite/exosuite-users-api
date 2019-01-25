<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class Jsonify
 * @package App\Http\Middleware
 */
class Jsonify
{
    /**
     * Change the Request headers to accept "application/json" first
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
