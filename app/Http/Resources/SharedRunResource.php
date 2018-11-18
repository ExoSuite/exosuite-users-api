<?php

namespace App\Http\Resources;

use App\Models\Share;
use Illuminate\Http\Resources\Json\JsonResource;

class SharedRunResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'visibility' => $this->visibility,
            'description' => $this->description,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'shared' => $this->share->first()
        ];
    }
}
