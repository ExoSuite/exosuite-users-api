<?php declare(strict_types = 1);

namespace App\Models;

use App\Http\Controllers\Time\TimeController;
use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\UserRun
 *
 * @property string $id
 * @property string $run_id
 * @property string $user_id
 * @property int $final_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Run $run
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Time[] $times
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereFinalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserRun whereUserId($value)
 * @mixin \Eloquent
 */
class UserRun extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        "id",
        "run_id",
        "user_id",
        "final_time",
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(static function (self $userRun): void {
            $userRun->times->each->delete();
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
