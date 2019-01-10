<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the message.
     *
     * @param  \App\Models\User $user
     * @param Message $message
     * @return mixed
     */
    public function view(User $user, Message $message)
    {
        return $message->group()->groupMembers()->whereUserId($user->id)->exists();
    }

    /**
     * Determine whether the user can create messages.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the message.
     *
     * @param  \App\Models\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function update(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can delete the message.
     *
     * @param  \App\Models\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function delete(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can restore the message.
     *
     * @param  \App\Models\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function restore(User $user, Message $message)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the message.
     *
     * @param  \App\Models\User $user
     * @param  \App\Message $message
     * @return mixed
     */
    public function forceDelete(User $user, Message $message)
    {
        //
    }
}
