<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use Uuids;

    protected $primaryKey = 'like_id';

    public $incrementing = false;

    protected $fillable = [
        'like_id', 'liked_id', "liked_type", 'liker_id'
    ];

    public function User()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
