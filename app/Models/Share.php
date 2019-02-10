<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Support\Facades\Auth;

/**
 * Class Share
 * @package App\Models
 */
class Share extends UuidModel
{

    /**
     * define share relation name
     */
    const SHARE_RELATION_NAME = 'shareable';

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'shareable_id', 'shareable_type',
        'created_at', 'updated_at'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'shareable_type', 'shareable_id'
    ];

    /**
     *
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $share) {
            if (!$share->user_id) {
                $share->user_id = Auth::id();
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shareable()
    {
        return $this->morphTo();
    }
}
