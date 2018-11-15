<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;

/**
 * Class Time
 * @package App\Models
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
}
