<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Time
 *
 * @package App\Models
 * @property \App\Models\Uuid $id
 * @property int $interval
 */
class Time extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'current_time',
        'check_point_id',
        'run_id',
    ];

    public function checkPoint(): BelongsTo
    {
        return $this->belongsTo(CheckPoint::class);
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }
}
