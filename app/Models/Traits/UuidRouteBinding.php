<?php declare(strict_types = 1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webpatser\Uuid\Uuid;

trait UuidRouteBinding
{

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function resolveRouteBinding($value)
    {
        $model = null;

        if (!Uuid::validate($value) || ($model = $this->where('id', $value)->first()) === null) {
            $this->onModelNotFound();
        }

        return $model;
    }

    private function onModelNotFound(): void
    {
        throw new ModelNotFoundException;
    }
}
