<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Friendship
 *
 * @package App\Models
 * @property string $id
 * @property string $user_id
 * @property string $friend_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $friend
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship whereFriendId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Friendship whereUserId($value)
 * @mixin \Eloquent
 */
class Friendship extends UuidModel
{

    use Uuids;

    /** @var string[] */
    protected $fillable = [
        'id',
        'user_id',
        'friend_id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleted(static function (self $friendship): void {
            $relation1 = static::whereFriendId($friendship->friend_id)->whereUserId($friendship->user_id);
            $relation2 = static::whereFriendId($friendship->user_id)->whereUserId($friendship->friend_id);

            if (!$relation1->exists() || !$relation2->exists()) {
                return;
            }

            $relation1->delete();
            $relation2->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
