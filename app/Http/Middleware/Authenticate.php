<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use App\Exceptions\AuthenticationException;

/**
 * Class Authenticate
 * @package App\Http\Middleware
 */
class Authenticate extends Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string[] ...$guards
     * @return mixed
     *
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate( $request, $guards );

        return $next( $request );
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array $guards
     * @return void
     *
     */
    protected function authenticate($request, array $guards)
    {
        if ( empty( $guards ) ) {
            $guards = [ null ];
        }

        foreach ( $guards as $guard ) {
            if ( $this->auth->guard( $guard )->check() ) {
                return $this->auth->shouldUse( $guard );
            }
        }

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards
        );
    }
}
