<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 18/09/2018
 * Time: 14:26
 */

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

trait JsonResponses
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContent()
    {
        return Response::json()
            ->setStatusCode(HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param array|Model|\Illuminate\Contracts\Auth\Authenticatable
     * @param string $location
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = [], string $location = null)
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        return Response::json($data)
            ->setStatusCode(HttpResponse::HTTP_CREATED)
            ->header('location', $location);
    }

    /**
     * @param array|Model|\Illuminate\Contracts\Auth\Authenticatable|Collection
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data)
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }
        
        return Response::json($data)
            ->setStatusCode(HttpResponse::HTTP_OK);
    }
}
