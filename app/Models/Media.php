<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Spatie\MediaLibrary\Models\Media as BaseMedia;

/**
 * Class Media
 * @package App\Models
 */
class Media extends BaseMedia
{
    use Uuids;

    /**
     * @var bool
     */
    public $incrementing = false;
}
