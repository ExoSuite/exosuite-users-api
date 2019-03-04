<?php declare(strict_types=1);

use App\Models\Group;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// private notification channel for a single user
Broadcast::channel('users.{id}', static function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('group.{group}', static function (User $user, Group $group) {
    if ($group->groupMembers()->whereUserId($user->id)->exists())
        return $group;
    return false;
});
