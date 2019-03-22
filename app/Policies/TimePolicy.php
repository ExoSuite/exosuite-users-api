<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Time;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the time.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Time $time
     * @return mixed
     */
    public function view(User $user, Time $time)
    {
    }

    /**
     * Determine whether the user can create times.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the time.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Time $time
     * @return mixed
     */
    public function update(User $user, Time $time)
    {
    }

    /**
     * Determine whether the user can delete the time.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Time $time
     * @return mixed
     */
    public function delete(User $user, Time $time)
    {
        return $user->id === $time->checkPoint->run->creator_id;
    }

    /**
     * Determine whether the user can restore the time.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Time $time
     * @return mixed
     */
    public function restore(User $user, Time $time)
    {
    }

    /**
     * Determine whether the user can permanently delete the time.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Time $time
     * @return mixed
     */
    public function forceDelete(User $user, Time $time)
    {
    }
}
