<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Commentary
 *
 * @package App\Models
 */
class Commentary extends Model
{
    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string[] */
    protected $fillable = [
        'id',
        'post_id',
        'content',
        'author_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return string[]
     */
    public function globalInfos(): array
    {
        $author = User::whereId($this->author_id)->first();
        $author_names = $author['first_name'] . ' ' . $author['last_name'];

        return [
            'content' => $this->content,
            'created_at' => $this->created_at,
            'author' => $author_names,
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
