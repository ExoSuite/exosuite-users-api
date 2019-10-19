<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:30
 */

namespace App\Services;

use App\Contracts\ApiHelperInterface;
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
 *
 * @package App\Services
 */
class ApiHelper implements ApiHelperInterface
{

    /** @var \App\Services\OAuth */
    private $OAuth;

    public static function getSessionDomain(): string
    {
        return '.' . self::getDomain();
    }

    public static function getDomain(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));

        return substr($parsed_url['host'], strpos($parsed_url['host'], '.') + 1);
    }

    public static function getHttpScheme(): string
    {
        $parsed_url = parse_url(env('APP_URL') ?? config('app.url'));

        return $parsed_url['scheme'];
    }

    /**
     * ApiHelper constructor.
     */
    public function __construct()
    {
        $this->OAuth = new class extends OAuth
        {
        };
    }

    public function OAuth(): OAuth
    {
        return $this->OAuth;
    }

    public function redirectToLogin(?string $redirectUrl = null): RedirectResponse
    {
        $scheme = self::getHttpScheme();
        $domain = self::getDomain();

        $redirectBack = $redirectUrl
            ? url($redirectUrl)
            : URL::full();

        return redirect()
            ->to("{$scheme}://{$domain}/login?redirect_uri={$redirectBack}");
    }

    public function redirectToWebsiteHome(): RedirectResponse
    {
        $scheme = self::getHttpScheme();
        $domain = self::getDomain();

        return redirect()->to("{$scheme}://{$domain}");
    }
}
