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
        if ($user->follows()->whereFollowedId($user->id)->exists()) {
            return $this->noContent();
        }

        return $this->created(Auth::user()->follows()->create(['followed_id' => $user->id]));
    }

    public function amIFollowing(User $user): JsonResponse
    {
        $status = Follow::whereUserId(Auth::user()->id)->whereFollowedId($user->id)->exists();

        return $this->ok(['status' => $status]);
    }

    public function getUserFollowers(User $user): JsonResponse
    {
        return $this->ok(Follow::whereFollowedId($user->id)->paginate());
    }

    public function countFollowers(User $user): JsonResponse
    {
        return $this->ok(["total" => Follow::whereUserId($user->id)->count()]);
    }

    public function delete(Follow $follow): JsonResponse
    {
        $follow->delete();

        return $this->noContent();
    }
}
