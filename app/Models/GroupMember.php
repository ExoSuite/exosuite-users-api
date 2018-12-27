<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

/**
 * Class GroupMember
 * @package App\Models
 * @property boolean is_admin
 * @property Uuid group_id
 * @property Uuid user_id
 */
class GroupMember extends UuidModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'group_id', 'user_id', 'is_admin', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool|mixed
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
}
