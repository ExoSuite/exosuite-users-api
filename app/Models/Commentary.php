<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Commentary
 * @package App\Models
 */
class Commentary extends Model
{
    use Uuids;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'post_id', 'content', 'author_id', 'created_at', 'updated_at'
    ];

    /**
     * @return array
     */
    public function globalInfos()
    {
        $author = User::whereId($this->author_id)->first();
        $author_names = $author['first_name'] . ' ' . $author['last_name'];

        return [
            'content' => $this->content,
            'created_at' => $this->created_at,
            'author' => $author_names
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
