<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class GroupPolicy
 *
 * @package App\Policies
 */
class GroupPolicy
{

    use HandlesAuthorization;

    public function createGroupMessage(User $user, Group $group): bool
    {
        $group_id = $group->id;

        return GroupMember::whereUserId($user->id)->whereGroupId($group_id)->exists();
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function viewGroupMessages(User $user, Group $group)
    {
        return $group->isMember($user);
    }

    /**
     * Determine whether the user can view the group.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
    }

    /**
     * Determine whether the user can create groups.
     *
     * @param \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the group.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function update(User $user, Group $group)
    {
        return $group->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the group.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return $group->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the group.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
    }

    /**
     * Determine whether the user can permanently delete the group.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
    }
}
