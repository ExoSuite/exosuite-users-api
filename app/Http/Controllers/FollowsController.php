<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class FollowsController
 *
 * @package App\Http\Controllers
 */
class FollowsController extends Controller
{

    public function store(User $user): JsonResponse
    {
        return !Follow::whereFollowedId($user->id)->whereUserId(Auth::user()->id)->exists() ? $this->created(Follow::create([
            "user_id" => Auth::user()->id,
            "followed_id" => $user->id,
        ])) : $this->badRequest("You're already following this user.");
    }

    public function AmIFollowing(User $user): JsonResponse
    {
        return Follow::whereUserId(Auth::user()->id)->whereFollowedId($user->id)->exists() ? $this->ok(['status' => true]) : $this->ok(['status' => false]);
    }

    public function WhoIsFollowing(User $user): JsonResponse
    {
        return Follow::whereFollowedId($user->id)->exists()
            ? $this->ok(Follow::whereFollowedId($user->id)->get()->pluck('user_id'))
            : $this->noContent();
    }

    public function delete(User $user): JsonResponse
    {
        $entity = Follow::whereUserId(Auth::user()->id)->whereFollowedId($user->id);

        if ($entity->exists()) {
            $entity->delete();

            return $this->noContent();
        }

        return $this->badRequest("You're not following this user.");
    }
}
