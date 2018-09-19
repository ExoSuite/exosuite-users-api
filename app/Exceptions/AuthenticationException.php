<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/09/2018
 * Time: 15:03
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AuthenticationException
 * @package App\Exceptions
 */
class AuthenticationException extends HttpException
{
    /**
     * All of the guards that were checked.
     *
     * @var array
     */
    protected $guards;

    /**
     * Create a new authentication exception.
     *
     * @param  string $message
     * @param  array $guards
     */
    public function __construct($message = 'Unauthenticated.', array $guards = [])
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $message);

        $this->guards = $guards;
    }

    /**
     * Get the guards that were checked.
     *
     * @return array
     */
    public function guards()
    {
        return $this->guards;
    }
}
