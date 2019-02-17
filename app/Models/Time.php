<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Webpatser\Uuid\Uuid;

/**
 * Class Time
 * @package App\Models
 * @property Uuid $id
 * @property int $interval
 */
class Time extends UuidModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'interval'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checkPoint()
    {
        return $this->belongsTo(CheckPoint::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function run()
    {
        return $this->belongsTo(Run::class);
    }
}
