<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;


class FollowsController extends Controller
{
    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user)
    {
        if (!Follow::whereFollowedId($user->id)->whereUserId(auth()->user()->id)->exists())
        {
            return $this->created(Follow::create([
                "user_id" => auth()->user()->id,
                "followed_id" => $user->id
            ]));
        }
        else
            return $this->badRequest("You're already following this user.");
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function AmIFollowing(User $user)
    {
        if (Follow::whereUserId(auth()->user()->id)->whereFollowedId($user->id)->exists())
            return $this->ok(['status' => true]);
        else
            return $this->ok(['status' => false]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function WhoIsFollowing(User $user)
    {
        if (Follow::whereFollowedId($user->id)->exists())
            return $this->ok(Follow::whereFollowedId($user->id)->get()->pluck('user_id'));
        else
            return $this->noContent();

    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(User $user)
    {
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($user->id);
        if ($entity->exists())
        {
            $entity->delete();
            return $this->noContent();
        }
        else
            return $this->badRequest("You're not following this user.");
    }
}
