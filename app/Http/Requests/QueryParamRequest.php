<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 21/09/2018
 * Time: 13:01
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class QueryParamRequest extends FormRequest
{
    public function all($keys = null)
    {
        return array_replace_recursive(
            parent::all(),
            $this->route()->parameters()
        );
    }
}
