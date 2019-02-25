<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 21/09/2018
 * Time: 13:01
 */

namespace App\Http\Requests\Abstracts;

use App\Http\Requests\Abstracts\RouteParamRequest;
use function array_except;

/**
 * Class RouteParamRequest
 *
 * @package App\Http\Requests\Abstracts
 */
abstract class RouteParamRequestUuidToId extends RouteParamRequest
{

    /**
     * @return array
     */
    public function validated(): array
    {
        $data = parent::validated();

        return array_except($data, 'id');
    }

    /**
     * @return mixed
     */
    protected function id()
    {
        return $this->all(['uuid'])['id'];
    }
}
