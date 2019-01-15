<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use Uuids;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
      'id', 'owner_id', 'restriction', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'id');
    }
}
