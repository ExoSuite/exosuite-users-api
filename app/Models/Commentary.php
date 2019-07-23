<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class Commentary
 *
 * @package App\Models
 */
class Commentary extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'post_id',
        'content',
        'author_id',
        'created_at',
        'updated_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(
            static function (Commentary $commentary): void {
                $commentary->likeFromUser()->delete();
            }
        );
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function likeFromUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            Like::class,
            User::class,
            'id',
            'liker_id',
            'author_id',
            'id'
        );
    }
}
