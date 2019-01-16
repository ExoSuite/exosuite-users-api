<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the notification.
     *
     * @param User $user
     * @return mixed
     */
    public function view(User $user)
    {
    }

    /**
     * Determine whether the user can create notifications.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the notification.
     *
     * @param  \App\Models\User $user
     * @param Notification $notification
     * @return mixed
     */
    public function update(User $user, Notification $notification)
    {
        return $user->id === $notification->notifiable_id;
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * @param  \App\Models\User $user
     * @param Notification $notification
     * @return mixed
     */
    public function delete(User $user, Notification $notification)
    {
        return $user->id === $notification->notifiable_id;
    }

    /**
     * Determine whether the user can restore the notification.
     *
     * @param  \App\Models\User $user
     * @param Notification $notification
     * @return mixed
     */
    public function restore(User $user, Notification $notification)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the notification.
     *
     * @param  \App\Models\User $user
     * @param Notification $notification
     * @return mixed
     */
    public function forceDelete(User $user, Notification $notification)
    {
        //
    }
}
