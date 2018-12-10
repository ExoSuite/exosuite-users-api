<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class LiveChatMessage extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $fillable = [
        'id', 'message_contents', 'author_id', 'created_at', 'modified_at'
    ];
}
