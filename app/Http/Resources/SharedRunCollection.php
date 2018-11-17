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
            $data = $run->only([
                'id',
                'name',
                'visibility',
                'visibility',
                'description',
                'creator_id'
            ]);

            $data['created_at'] = $run->created_at->toDateTimeString();
            $data['updated_at'] = $run->updated_at->toDateTimeString();
            $data['shared']['user_id'] = $run->pivot->user_id;
            return $data;
        })->toArray();
    }
}
