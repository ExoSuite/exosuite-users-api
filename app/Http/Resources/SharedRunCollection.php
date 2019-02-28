<?php

namespace App\Http\Resources;

use App\Models\Run;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class SharedRunCollection
 *
 * @package App\Http\Resources
 */
class SharedRunCollection extends ResourceCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(static function (Run $run) use ($request) {
            return (new SharedRunResource($run))->toArray($request);
        })->toArray();
    }
}
