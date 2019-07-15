<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Follow
 *
 * @package App\Models
 * @property string $id
 * @property string $user_id
 * @property string $followed_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $followers
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow whereFollowedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Follow whereUserId($value)
 * @mixin \Eloquent
 */
class Follow extends UuidModel
{

    use Uuids;

    /** @var string[] */
    protected $fillable = [
        'id',
        'user_id',
        'followed_id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleted(static function (self $follow): void {
            $follow = static::whereUserId($follow->user_id)->whereFollowedId($follow->followed_id);

            if (!$follow->exists()) {
                return;
            }

            $follow->delete();
        });
    }

    public function followers(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
