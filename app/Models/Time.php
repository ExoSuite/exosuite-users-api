<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Time
 *
 * @package App\Models
 * @property \App\Models\Uuid $id
 * @property int $current_time
 * @property string $check_point_id
 * @property string $run_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CheckPoint $checkPoint
 * @property-read \App\Models\Run $run
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereCheckPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereCurrentTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Time whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Time extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'current_time',
        'check_point_id',
        'run_id',
        'user_run_id',
    ];

    public function checkPoint(): BelongsTo
    {
        return $this->belongsTo(CheckPoint::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function userRun(): BelongsTo
    {
        return $this->belongsTo(UserRun::class);
    }
}
