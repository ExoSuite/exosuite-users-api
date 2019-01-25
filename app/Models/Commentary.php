<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Commentary extends Model
{
    use Uuids;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id', 'post_id', 'content', 'author_id', 'created_at', 'updated_at'
    ];

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

    public function post()
    {
        return $this->belongsTo(Post::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
