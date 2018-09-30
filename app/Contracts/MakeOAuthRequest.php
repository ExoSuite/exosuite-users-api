<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:35
 */

namespace App\Contracts;

use App\Models\User;

/**
 * Interface MakeOAuthRequest
 * @package App\Contracts
 */
interface MakeOAuthRequest
{
    /**
     * @param User $user
     * @param int $client_id
     * @param string $client_secret
     * @param int $statusCode
     * @return string|mixed
     */
    public function passwordGrant(
        User $user,
        int $client_id,
        string $client_secret,
        int $statusCode = Response::HTTP_OK
    );
}
