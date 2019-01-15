<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $primaryKey = 'follow_id';

    protected $fillable = [
      'follow_id', 'user_id', 'followed_id'
    ];

}
