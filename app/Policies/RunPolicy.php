<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\Run;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RunPolicy
 *
 * @package App\Policies
 */
class RunPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the run.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Run $run
     *
     * @return mixed
     */
    public function view(User $user, Run $run)
    {
    }

    /**
     * Determine whether the user can create runs.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the run.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Run $run
     *
     * @return mixed
     */
    public function update(User $user, Run $run)
    {
        return $run->creator_id === $user->id;
    }

    /**
     * Determine whether the user can delete the run.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Run $run
     *
     * @return mixed
     */
    public function delete(User $user, Run $run)
    {
        return $run->creator_id === $user->id;
    }

    /**
     * Determine whether the user can restore the run.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Run $run
     *
     * @return mixed
     */
    public function restore(User $user, Run $run)
    {
    }

    /**
     * Determine whether the user can permanently delete the run.
     *
     * @param  \App\Models\User $user
     * @param \App\Models\Run $run
     *
     * @return mixed
     */
    public function forceDelete(User $user, Run $run)
    {
    }
}
