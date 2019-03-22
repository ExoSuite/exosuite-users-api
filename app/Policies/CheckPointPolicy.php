<?php declare(strict_types = 1);

namespace App\Policies;

use App\Models\CheckPoint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CheckPointPolicy
 *
 * @package App\Policies
 */
class CheckPointPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the check point.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\CheckPoint $checkPoint
     * @return mixed
     */
    public function view(User $user, CheckPoint $checkPoint)
    {
    }

    /**
     * Determine whether the user can create check points.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the check point.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\CheckPoint $checkPoint
     * @return mixed
     */
    public function update(User $user, CheckPoint $checkPoint)
    {
        return $user->id === $checkPoint->run->creator_id;
    }

    /**
     * Determine whether the user can delete the check point.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\CheckPoint $checkPoint
     * @return mixed
     */
    public function delete(User $user, CheckPoint $checkPoint)
    {
        return $user->id === $checkPoint->run->creator_id;
    }

    /**
     * Determine whether the user can restore the check point.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\CheckPoint $checkpoint
     * @return mixed
     */
    public function restore(User $user, CheckPoint $checkpoint)
    {
    }

    /**
     * Determine whether the user can permanently delete the check point.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\CheckPoint $checkpoint
     * @return mixed
     */
    public function forceDelete(User $user, CheckPoint $checkpoint)
    {
    }
}
