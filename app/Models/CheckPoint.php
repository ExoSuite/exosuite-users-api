<?php

namespace App\Models;

use App\Enums\CheckPointType;
use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * Class CheckPoint
 * @package App\Models
 */
class CheckPoint extends UuidModel
{
    use PostgisTrait;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['id', 'type', 'location'];

    /**
     * @var array
     */
    protected $postgisFields = [
        'location'
    ];

    /**
     * if no type is provided fill with default value
     */
    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $checkPoint) {
            if (!$checkPoint->type) {
                $checkPoint->type = CheckPointType::DEFAULT;
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function run()
    {
        return $this->belongsTo(Run::class);
    }

    public function times()
    {
        return $this->hasMany(Time::class);
    }
}
