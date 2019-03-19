<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;

/**
 * Class Follow
 *
 * @package App\Models
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
}