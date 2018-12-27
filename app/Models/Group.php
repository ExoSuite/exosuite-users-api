<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Pivots\GroupMembers;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * @package App\Models
 */
class Group extends UuidModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, GroupMember::class, "group_id", 'id', "id", "user_id");
    }
}
