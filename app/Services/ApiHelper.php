<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:30
 */

namespace App\Services;

use App\Services\ApiHelperInterface;
use App\Services\OAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use function config;
use function env;
use function redirect;
use function strpos;
use function substr;
use function url;

/**
 * Class ApiHelper
 * @package App\Services
 */
class ApiHelper implements ApiHelperInterface
{
    /**
     * @var \App\Services\OAuth
     */
    private $_OAuth;

    /**
     * ApiHelper constructor.
     */
    public function __construct()
    {
        $this->_OAuth = new class extends OAuth
        {
        };
    }

    public static function getSessionDomain(): string
    {
        return '.' . self::getDomain();
    }

    public static function getDomain(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));
        $domain = substr($parsed_url['host'], strpos($parsed_url['host'], '.') + 1);

        if (config('app.env') === 'staging') {
            $domain = "website.{$domain}";
        }

        return $domain;
    }

    public function OAuth(): OAuth
    {
        return $this->_OAuth;
    }

    public function redirectToLogin(?string $redirectUrl = null): RedirectResponse
    {
        $scheme = ApiHelper::getHttpScheme();
        $domain = ApiHelper::getDomain();

        $redirectBack = $redirectUrl ? url($redirectUrl) : URL::full();

        return redirect()
            ->to("{$scheme}://{$domain}/login?redirect_uri={$redirectBack}");
    }

    public static function getHttpScheme(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));

        return $parsed_url["scheme"];
    }
}
