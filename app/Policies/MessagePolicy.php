<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MessagePolicy
 *
 * @package App\Policies
 */
class MessagePolicy
{

    use HandlesAuthorization;

    /**
     * Determine whether the user can view the message.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Message $message
     * @return mixed
     */
    public function view(User $user, Message $message)
    {
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function create(User $user, Group $group)
    {
    }

    /**
     * Determine whether the user can update the message.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Message $message
     * @return mixed
     */
    public function update(User $user, Message $message)
    {
        return $user->id === $message->user_id;
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Message $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        return $user->id === $message->user_id || $message->group->first()->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the message.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Message $message
     * @return mixed
     */
    public function restore(User $user, Message $message)
    {
    }

    /**
     * Determine whether the user can permanently delete the message.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Message $message
     * @return mixed
     */
    public function forceDelete(User $user, Message $message)
    {
    }
}
