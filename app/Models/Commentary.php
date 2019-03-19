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

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
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
