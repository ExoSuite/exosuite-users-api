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
        $locale = $request->header('X-Localization') ?: 'en';
        // set laravel localization
        app()->setLocale($locale);

        // continue request
        return $next($request);
    }
}
