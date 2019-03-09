<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Post
 *
 * @package App\Models
 */
class Post extends Model
{
    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string[] */
    protected $fillable = [
        'id',
        'dashboard_id',
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

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class, 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function commentaries(): HasMany
    {
        return $this->hasMany(Commentary::class, 'id');
    }
}
