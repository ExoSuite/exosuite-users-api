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
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Trait JsonResponses
 * @package App\Http\Controllers\Traits
 */
trait JsonResponses
{

    /**
     * @param $data
     * @return array
     */
    private function toArray($data)
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        if ($data instanceof ResourceCollection) {
            $data = $data->toArray(request());
        }

        return $data;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContent()
    {
        return Response::json()
            ->setStatusCode(HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param array|Model|\Illuminate\Contracts\Auth\Authenticatable|ResourceCollection
     * @param string $location
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = [], string $location = null)
    {
        return Response::json($this->toArray($data))
            ->setStatusCode(HttpResponse::HTTP_CREATED)
            ->header('location', $location);
    }

    /**
     * @param array|Model|\Illuminate\Contracts\Auth\Authenticatable|Collection
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data)
    {
        return Response::json($this->toArray($data))
            ->setStatusCode(HttpResponse::HTTP_OK);
    }
}
