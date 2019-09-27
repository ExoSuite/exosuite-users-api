<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Record
 *
 * @property-read \App\Models\Run $run
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record query()
 * @mixin \Eloquent
 * @property string $id
 * @property string $run_id
 * @property int $best_time
 * @property string $best_time_user_run_id
 * @property int $sum_of_best
 * @property string $user_id
 * @property array $best_segments
 * @property float $total_distance
 * @property float $average_speed_on_best_time
 * @property array $distance_between_cps
 * @property array $best_speed_between_cps
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereAverageSpeedOnBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereBestSegments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereBestSpeedBetweenCps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereBestTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereBestTimeUserRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereDistanceBetweenCps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereSumOfBest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereTotalDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Record whereUserId($value)
 */
class Record extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'run_id',
        'best_time',
        'best_time_user_run_id',
        'sum_of_best',
        'user_id',
        'best_segments',
        'total_distance',
        'average_speed_on_best_time',
        'distance_between_cps',
        'best_speed_between_cps',
    ];

    /** @var string[] */
    protected $casts = [
        'best_segments' => 'array',
        'distance_between_cps' => 'array',
        'best_speed_between_cps' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }
}
