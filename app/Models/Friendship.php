<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Uuids;

class Friendship extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $primaryKey = 'friendship_id';

    protected $fillable = [
        'friendship_id', 'user_id', 'friend_id'
    ];
}
