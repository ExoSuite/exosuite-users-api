<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 18/09/2018
 * Time: 14:26
 */

namespace App\Http\Controllers\Traits;

use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function fclose;
use function fpassthru;
use function is_resource;

/**
 * Trait JsonResponses
 *
 * @package App\Http\Controllers\Traits
 */
trait JsonResponses
{

    protected function noContent(): JsonResponse
    {
        return Response::json()
            ->setStatusCode(HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $data
     * @param string $location
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = [], ?string $location = null): JsonResponse
    {
        return Response::json($this->toArray($data))
            ->setStatusCode(HttpResponse::HTTP_CREATED)
            ->header('location', $location);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data): JsonResponse
    {
        return Response::json($this->toArray($data))
            ->setStatusCode(HttpResponse::HTTP_OK);
    }

    /**
     * @param \App\Models\Media $media
     * @param string $conversionName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    protected function file(Media $media, string $conversionName = ''): StreamedResponse
    {
        return Response::stream(static function () use ($media, $conversionName): void {
            $stream = Storage::readStream($media->toStreamPath($conversionName));

            fpassthru($stream);

            if (!is_resource($stream)) {
                return;
            }

            fclose($stream);
        }, 200, $media->toStreamHeaders($conversionName));
    }

    protected function localFile(string $path): StreamedResponse
    {
        $fileinfo = pathinfo($path);
        $extension = $fileinfo["extension"];
        $filename = $fileinfo["basename"];

        return Response::stream(static function () use ($path): void {
            $stream = Storage::disk('local')->readStream($path);

            fpassthru($stream);

            if (!is_resource($stream)) {
                return;
            }

            fclose($stream);
        }, 200, [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => "image/$extension",
            'Content-Length' => Storage::disk('local')->size($path),
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'public',
        ]);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function toArray($data)
    {
        if (is_object($data) && !($data instanceof ResourceCollection)) {
            $data = $data->toArray();
        }

        return $data;
    }
}
