<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 15:13
 */

namespace App\Services;

use App\Contracts\MakeOAuthRequest;
use App\Exceptions\InternalRequestException;
use App\Facades\InternalRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class OAuth
 *
 * @package App\Services
 */
abstract class OAuth implements MakeOAuthRequest
{

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $client_id
     * @param string $client_secret
     * @param int $statusCode
     * @return mixed
     */
    final public function passwordGrant(
        Authenticatable $user,
        int $client_id,
        string $client_secret,
        int $statusCode = Response::HTTP_OK
    )
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $user->email,
            'password' => $user->password,
            'scope' => '',
        ];

        try {
            return InternalRequest::request(Request::METHOD_POST, 'oauth/token', $data, [], $statusCode);
        } catch (InternalRequestException $exception) {
            return $exception->getResponse();
        }
    }
}
