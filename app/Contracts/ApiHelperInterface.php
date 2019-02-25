<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 15:10
 */

namespace App\Contracts;

/**
 * Interface ApiHelperInterface
 *
 * @package App\Contracts
 */
interface ApiHelperInterface
{

    /**
     * @return \App\Services\OAuth
     */
    public function OAuth(): OAuth;
}
