<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\CheckPointType;
use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @var string[]
     */
    protected $fillable = [
        'id', 'type', 'location'
    ];

    /**
     * @var string[]
     */
    protected $postgisFields = [
        'location'
    ];

    /**
     * if no type is provided fill with default value
     */
    protected static function boot(): void
    {
        parent::boot();
        self::creating(static function (self $checkPoint): void {
            if ($checkPoint->type) {
                return;
            }

            $checkPoint->type = CheckPointType::DEFAULT;
        });
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(Time::class);
    }
}
