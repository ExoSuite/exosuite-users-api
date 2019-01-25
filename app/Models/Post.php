<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Uuids;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id', 'dashboard_id', 'content', 'author_id', 'created_at', 'updated_at'
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

    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function commentaries()
    {
        return $this->hasMany(Commentary::class, 'id');
    }
}
