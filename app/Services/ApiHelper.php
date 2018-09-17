<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 14:30
 */

namespace App\Services;


use App\Contracts\ApiHelperInterface;

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
}
