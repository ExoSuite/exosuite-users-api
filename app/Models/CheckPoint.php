<?php

namespace App\Models;

use App\Enums\CheckPointType;
use App\Models\Abstracts\UuidModel;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;
use Phaza\LaravelPostgis\Geometries\Polygon;
use Webpatser\Uuid\Uuid;

/**
 * Class CheckPoint
 * @package App\Models
 * @property Uuid $id
 * @property CheckPointType $type
 * @property Polygon $location
 */
class CheckPoint extends UuidModel
{
    use PostgisTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'type', 'location'
    ];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function times()
    {
        return $this->hasMany(Time::class);
    }
}
