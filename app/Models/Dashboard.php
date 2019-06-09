<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Dashboard
 *
 * @package App\Models
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
