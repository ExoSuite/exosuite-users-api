<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class Jsonify
 *
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
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
