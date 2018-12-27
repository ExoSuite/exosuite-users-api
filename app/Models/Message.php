<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Message extends UuidModel
{
    protected $fillable = [
        'id', 'contents', 'author_id', 'created_at', 'updated_at'
    ];
}
