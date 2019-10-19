<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cookie;
use function app;

class Localization
{

    public static $locales = ['fr', 'en'];
    const KEY = 'locale';

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
        $locale = Cookie::get(self::KEY);
        if (!$locale) {
            $locale = $request->getPreferredLanguage(self::$locales);
            app()->setLocale($locale);
        }

        if (!$locale) {
            // Check header request and determine localizaton
            $locale = $request->header('X-Localization') ?: 'en';
            // set laravel localization
            app()->setLocale($locale);
        }

        // continue request
        return $next($request);
    }
}
