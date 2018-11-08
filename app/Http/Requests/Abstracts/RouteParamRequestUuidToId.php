<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 21/09/2018
 * Time: 13:01
 */

namespace App\Http\Requests\Abstracts;


/**
 * Class RouteParamRequest
 * @package App\Http\Requests\Abstracts
 */
abstract class RouteParamRequestUuidToId extends RouteParamRequest
{
    /**
     * @return array
     */
    public function validated()
    {
        $data = parent::validated();
        return array_except($data, 'id');
    }

    protected function id()
    {
        return $this->all(['uuid'])['id'];
    }
}
