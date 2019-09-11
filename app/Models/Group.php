<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Group
 *
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupMember[] $groupMembers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $latestMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Group whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read int|null $group_members_count
 * @property-read int|null $latest_messages_count
 * @property-read int|null $messages_count
 * @property-read int|null $users_count
 */
class Group extends UuidModel
{

    public const MAX_MESSAGE_PER_PAGE = 15;

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

    public function latestMessages(): HasMany
    {
        return $this->messages()->orderBy('created_at')->limit(self::MAX_MESSAGE_PER_PAGE);
    }

    public function loadGroupMembersAndLatestMessages(): self
    {
        return $this->load(['groupMembers', 'latestMessages']);
    }
}
