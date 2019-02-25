<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\Visibility;
use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Shareable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Class Run
 * @package App\Models
 * @property Uuid $id
 * @property string $description
 * @property Uuid $creator_id
 * @property Visibility $visibility
 * @property string $name
 */
class Run extends UuidModel
{
    use Shareable;

    /**
     * define relation key
     */
    const USER_FOREIGN_KEY = 'creator_id';

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'description', 'creator_id', 'visibility', 'name', 'updated_at', 'created_at'
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
        return $this->hasMany(CheckPoint::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_FOREIGN_KEY);
    }
}
