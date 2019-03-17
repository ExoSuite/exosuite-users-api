<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;

/**
 * Class Friendship
 *
 * @package App\Models
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
}
