<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * Class Commentary
 *
 * @package App\Models
 * @property string $id
 * @property string $post_id
 * @property string $content
 * @property string $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read \App\Models\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Commentary whereUpdatedAt($value)
 * @mixin \Eloquent
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
