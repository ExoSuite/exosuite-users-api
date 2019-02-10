<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;

/**
 * Class GroupMember
 * @property bool $is_admin
 * @package App\Models
 */
class GroupMember extends UuidModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'group_id', 'user_id', 'is_admin', 'created_at', 'updated_at'
    ];

    /**
     * @var array
     */
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
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
}
