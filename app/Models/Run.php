<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\Visibility;
use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Shareable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Auth;

/**
 * Class Run
 *
 * @package App\Models
 * @property \App\Models\Uuid $id
 * @property string $description
 * @property string $creator_id
 * @property \App\Enums\Visibility $visibility
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CheckPoint[] $checkpoints
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Share[] $share
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Time[] $timesThroughCheckpoints
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserRun[] $userRuns
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereVisibility($value)
 * @mixin \Eloquent
 */
class Run extends UuidModel
{
    use Shareable;

    /**
     * define relation key
     */
    public const USER_FOREIGN_KEY = 'creator_id';

    /** @var string[] */
    protected $fillable = [
        'id',
        'description',
        'creator_id',
        'visibility',
        'name',
        'updated_at',
        'created_at',
    ];

    /**
     * if no visibility is provided set to private
     */
    protected static function boot(): void
    {
        parent::boot();
        self::creating(static function (self $run): void {
            if (!$run->visibility) {
                $run->visibility = Visibility::PRIVATE;
            }

            $run->creator_id = Auth::id();
        });
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(CheckPoint::class)->with(['times']);
    }

    public function timesThroughCheckpoints(): HasManyThrough
    {
        return $this->hasManyThrough(
            Time::class,
            CheckPoint::class,
            'run_id',
            'check_point_id',
            'id',
            'id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_FOREIGN_KEY);
    }

    public function likeFromUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            Like::class,
            User::class,
            'id',
            'liker_id',
            'creator_id',
            'id'
        );
    }

    public function likes(): HasMany
    {
        return $this->hasMany(
            Like::class,
            'liked_id'
        );
    }

    public function userRuns(): HasMany
    {
        return $this->hasMany(UserRun::class)->with(['times']);
    }

}
