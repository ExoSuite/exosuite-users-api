<?php

use App\Models\Group;
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
Broadcast::channel('users.{id}', function ($user, $id) {
    return $user->id === $id;
});

Broadcast::channel('group.{group_id}', function ($user, Group $group) {
    return $group->groupMembers()->whereUserId($user)->exists();
});
