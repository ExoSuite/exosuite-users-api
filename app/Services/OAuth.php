<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 15:13
 */

namespace App\Services;


use App\Contracts\MakeOAuthRequest;
use App\Models\User;
use App\Facades\InternalRequest;
use App\Exceptions\InternalRequestException;
use Laravel\Passport\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class OAuth
 * @package App\Services
 */
abstract class OAuth implements MakeOAuthRequest
{
    /**
     * @var Client
     */
    protected $_oauth_client = null;


    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->_oauth_client = Client::whereId(2)->first();
    }

    /**
     * @param User $user
     * @param int $statusCode
     * @return mixed
     */
    final public function passwordGrant(User $user, int $statusCode = Response::HTTP_OK)
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => 0,
            'client_secret' => null,
            'username' => $user->email,
            'password' => $user->password,
            'scope' => '',
        ];

        if ( $this->_oauth_client )
            $data = array_merge(
                $data, [
                    'client_id' => $this->_oauth_client->getAttribute('id'),
                    'client_secret' => $this->_oauth_client->getAttribute('secret'),
                ]
            );

        try {
            return InternalRequest::request(Request::METHOD_POST, 'oauth/token', $data, [], $statusCode);
        } catch ( InternalRequestException $exception ) {
            return $exception->getResponse();
        }
    }

}