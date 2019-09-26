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
        'best_speed_between_cps' => 'array',
        'distance_between_cps' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }
}
