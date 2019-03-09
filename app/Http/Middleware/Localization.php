<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use function app;

class Localization
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
        // Check header request and determine localizaton
        $local = $request->hasHeader('X-localization') ?: 'en_US';
        // set laravel localization
        app()->setLocale($local);

        // continue request
        return $next($request);
    }
}
