<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\CheckPointType;
use App\Http\Controllers\Time\TimeController;
use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * Class CheckPoint
 *
 * @package App\Models
 * @property \Webpatser\Uuid\Uuid $id
 * @property \App\Enums\CheckPointType $type
 * @property \Phaza\LaravelPostgis\Geometries\Polygon $location
 * @property string $run_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $previous_checkpoint_id
 * @property-read \App\Models\Run $run
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Time[] $times
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\CheckPoint newModelQuery()
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\CheckPoint newQuery()
 * @method static \Phaza\LaravelPostgis\Eloquent\Builder|\App\Models\CheckPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint wherePreviousCheckpointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CheckPoint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CheckPoint extends UuidModel
{
    use PostgisTrait;

    /** @var string[] */
    protected $fillable = [
        'id',
        'type',
        'location',
        "run_id",
        "previous_checkpoint_id",
    ];

    /** @var string[] */
    protected $postgisFields = [
        'location',
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
        static::deleting(static function (self $checkpoint): void {
            $checkpoint->times->each->delete();
        });
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(Time::class)->limit(TimeController::GET_PER_PAGE);
    }
}
