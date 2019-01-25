<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Spatie\MediaLibrary\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use Uuids;

    public $incrementing = false;
}
