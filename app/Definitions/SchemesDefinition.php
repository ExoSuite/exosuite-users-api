<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 16/09/2018
 * Time: 15:51
 */

use Webpatser\Uuid\Uuid;

/**
 * @OA\Schema(
 *     schema="TokenResponse",
 *     required={"token_type", "expires_in", "access_token", "refresh_token"},
 *  )
 */
class TokenResponse
{
    /**
     * @var string
     * @OA\Property()
     */
    public $token_type;

    /**
     * @var integer
     * @OA\Property()
     */
    public $expires_in;

    /**
     * @var string
     * @OA\Property()
     */
    public $access_token;

    /**
     * @var string
     * @OA\Property()
     */
    public $refresh_token;
}

/**
 * @OA\Schema(
 *     schema="NewUser",
 *     required={"first_name","last_name", "email", "password","password_confirmation"}
 *  )
 */
class NewUser
{

    /**
     * @var string
     * @OA\Property()
     */
    public $first_name;

    /**
     * @var string
     * @OA\Property()
     */
    public $last_name;

    /**
     * @var string
     * @OA\Property()
     */
    public $email;

    /**
     * @var string
     * @OA\Property()
     */
    public $nick_name;

    /**
     * @var string
     * @OA\Property()
     */
    public $password;

    /**
     * @var string
     * @OA\Property()
     */
    public $password_confirmation;
}

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     required={"message", "errors"},
 *  )
 */
class ErrorResponse
{

    /**
     * @var string
     * @OA\Property(example="The given data was invalid.")
     */
    public $message;

    /**
     * @var array
     * @OA\Property(
     *          @OA\Items(type="string"),
     *          example={
     *              "email": {"The email field is required."},
     *              "password": {"The password field is required."}
     *           }
     * )
     */
    public $errors;
}

/**
 * @OA\Schema(
 *     schema="Login",
 *     required={"email", "password"},
 *  )
 */
class Login
{

    /**
     * @var string
     * @OA\Property(example="foo@bar.com")
     */
    public $email;

    /**
     * @var string
     * @OA\Property()
     */
    public $password;
}

/**
 * @OA\Schema(
 *     schema="UserProfile"
 *  )
 */
class UserProfile
{
    /**
     * @var DateTime
     * @OA\Property(description="Date in UTC Format YYYY-MM-DD")
     */
    public $birthday;

    /**
     * @var string
     * @OA\Property()
     */
    public $description;

    /**
     * @var string
     * @OA\Property()
     */
    public $city;
}

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"first_name","last_name", "email", "nick_name", "id"}
 *  )
 */
class User
{
    /**
     * @var string
     * @OA\Property()
     */
    public $email;

    /**
     * @var string
     * @OA\Property()
     */
    public $first_name;

    /**
     * @var string
     * @OA\Property()
     */
    public $last_name;

    /**
     * @var string
     * @OA\Property()
     */
    public $nick_name;

    /**
     * @var Uuid
     * @OA\Property()
     */
    public $id;
}
