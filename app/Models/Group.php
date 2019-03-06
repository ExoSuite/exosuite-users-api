<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Group
 *
 * @package App\Models
 */
class Group extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(static function (self $group): void {
            $group->groupMembers->each->delete();
            $group->messages->each->delete();
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            GroupMember::class,
            'group_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * @param \App\Models\User $user
     * @return mixed
     */
    public function isAdmin(User $user)
    {
        if ($this->isMember($user)) {
            return GroupMember::whereUserId($user->id)->first()->isAdmin();
        }

        return false;
    }

    /**
     * @param \App\Models\User $user
     * @return mixed
     */
    public function isMember(User $user)
    {
        return $this->groupMembers()->whereUserId($user->id)->exists();
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }
}
