<?php

namespace App\Http\Resources;

use App\Models\Run;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class SharedRunCollection
 * @package App\Http\Resources
 */
class SharedRunCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (Run $run) {
            return new SharedRunResource($run);
        })->toArray();
    }
}
