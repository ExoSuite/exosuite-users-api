<?php declare(strict_types = 1);

namespace App\Observers;

use App\Models\User;
use Hash;
use Webpatser\Uuid\Uuid;

class UserObserver
{

    /**
     * Handle the user "creating" event.
     *
     * @param \App\Models\User $user
     * @return void
     * @throws \Exception
     */
    public function creating(User $user): void
    {
        $user->password = Hash::make($user->password);
        $user->{$user->getKeyName()} = Uuid::generate()->string;
    }

    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user): void
    {
        $user->profile()->create();
        $user->dashboard()->create();
        $user->profileRestrictions()->create();
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user): void
    {
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user): void
    {
        $user->dashboard()->delete();
        $user->runs()->delete();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user): void
    {
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user): void
    {
    }
}
