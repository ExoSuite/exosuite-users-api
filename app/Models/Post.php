<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class Post
 *
 * @package App\Models
 */
class Post extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'dashboard_id',
        'content',
        'author_id',
        'created_at',
        'updated_at',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentaries(): HasMany
    {
        return $this->hasMany(Commentary::class);
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
