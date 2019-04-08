<?php declare(strict_types = 1);

namespace App\Passport;


class RouteRegistrar extends \Laravel\Passport\RouteRegistrar
{

    public function forPersonalAccessTokens(): void
    {
        $this->router->group(['middleware' => ['auth:api']], static function ($router): void {
            $router->get('/scopes', [
                'uses' => 'ScopeController@all',
                'as' => 'passport.scopes.index',
            ]);
            $router->get('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@forUser',
                'as' => 'passport.personal.tokens.index',
            ]);
            $router->post('/personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@store',
                'as' => 'passport.personal.tokens.store',
            ]);
            $router->delete('/personal-access-tokens/{token_id}', [
                'uses' => 'PersonalAccessTokenController@destroy',
                'as' => 'passport.personal.tokens.destroy',
            ]);
        });
    }
}
