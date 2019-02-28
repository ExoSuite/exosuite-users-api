<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

/**
 * Class Share
 *
 * @package App\Models
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
