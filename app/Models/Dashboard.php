<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Dashboard
 *
 * @package App\Models
 * @property string $id
 * @property string $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $visibility
 * @property string $writing_restriction
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Dashboard whereWritingRestriction($value)
 * @mixin \Eloquent
 * @property-read int|null $posts_count
 */
class Dashboard extends UuidModel
{

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string[] */
    protected $fillable = [
        'id',
        'owner_id',
        'visibility',
        'writing_restriction',
        'created_at',
        'updated_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(
            static function (Dashboard $dashboard): void {
                $dashboard->posts()->delete();
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
