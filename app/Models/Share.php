<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

/**
 * Class Share
 *
 * @package App\Models
 * @property string $id
 * @property string $user_id
 * @property string $shareable_id
 * @property string $shareable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Share[] $shareable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereShareableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereShareableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Share whereUserId($value)
 * @mixin \Eloquent
 */
class Share extends UuidModel
{

    /**
     * define share relation name
     */
    public const SHARE_RELATION_NAME = 'shareable';

    /** @var string[] */
    protected $fillable = [
        'id',
        'user_id',
        'shareable_id',
        'shareable_type',
        'created_at',
        'updated_at',
    ];

    /** @var string[] */
    protected $hidden = [
        'shareable_type',
        'shareable_id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(static function (self $share): void {
            if ($share->user_id) {
                return;
            }

            $share->user_id = Auth::id();
        });
    }

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }
}
