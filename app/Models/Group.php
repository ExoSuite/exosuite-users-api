<?php

namespace App\Models;

use App\Models\Abstracts\UuidModel;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, GroupMember::class, "group_id", 'id', "id", "user_id");
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function isAdmin(User $user)
    {
        return $this->groupMembers()->where("user_id", $user->id)->isAdmin;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function isMember(User $user)
    {
        return $this->groupMembers()->whereUserId($user->id)->exists();
    }
}
