<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:30
 */

namespace App\Services;


use App\Contracts\ApiHelperInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

/**
 * Class ApiHelper
 * @package App\Services
 */
class ApiHelper implements ApiHelperInterface
{
    /**
     * @var OAuth
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

    /**
     * @return OAuth
     */
    public function OAuth()
    {
        return $this->_OAuth;
    }

    /**
     * @return string
     */
    public static function getDomain(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));
        $domain = substr($parsed_url['host'], strpos($parsed_url['host'], '.') + 1);
        if (config('app.env') === 'staging') {
            $domain = "website.{$domain}";
        }
        return $domain;
    }

    /**
     * @return string
     */
    public static function getSessionDomain(): string
    {
        return '.' . self::getDomain();
    }

    /**
     * @return string
     */
    public static function getHttpScheme(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));
        return $parsed_url["scheme"];
    }

    /**
     * @param string|null $redirectUrl
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToLogin($redirectUrl = null)
    {
        $scheme = ApiHelper::getHttpScheme();
        $domain = ApiHelper::getDomain();

        if ($redirectUrl) {
            $redirectBack = url($redirectUrl);
        } else {
            $redirectBack = URL::full();
        }

        return redirect()
            ->to("{$scheme}://{$domain}/login?redirect_uri={$redirectBack}");
    }
}
