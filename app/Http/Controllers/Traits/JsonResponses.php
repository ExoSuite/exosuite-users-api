<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 18/09/2018
 * Time: 14:26
 */

namespace App\Http\Controllers\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Trait JsonResponses
 * @package App\Http\Controllers\Traits
 */
trait JsonResponses
{

    /**
     * @param mixed $data
     * @return array
     */
    private function toArray($data)
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
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
     * @param array|Model|\Illuminate\Contracts\Auth\Authenticatable $data
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
     * @param array|Model|Authenticatable|Collection|ResourceCollection|JsonResource|LengthAwarePaginator $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data)
    {
        return Response::json($this->toArray($data))
            ->setStatusCode(HttpResponse::HTTP_OK);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message)
    {
        return Response::json(['message' => $message])
            ->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden(string $message)
    {
        return Response::json(['message' => $message])->setStatusCode(HttpResponse::HTTP_FORBIDDEN);
    }

    /**
     * @param Media $media
     * @param string $conversionName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    protected function file(Media $media, string $conversionName = '')
    {
        return Response::stream(function () use ($media, $conversionName) {
            $stream = Storage::readStream($media->toStreamPath($conversionName));

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $media->toStreamHeaders($conversionName));
    }
}
