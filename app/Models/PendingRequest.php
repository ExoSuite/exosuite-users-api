<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class PendingRequest extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_id', 'requester_id', 'type', 'target_id'
    ];
}
