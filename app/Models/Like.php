<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Like
 * @package App\Models
 */
class Like extends Model
{
    use Uuids;

    /**
     * @var string
     */
    protected $primaryKey = 'like_id';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'like_id', 'liked_id', "liked_type", 'liker_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
