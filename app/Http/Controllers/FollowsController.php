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
        $requerant = Auth::user();

        if ($user->follows()->whereUserId($requerant->id)->whereFollowedId($user->id)->exists()) {
            return $this->noContent();
        }

        return $this->created($requerant->follows()->create(['followed_id' => $user->id]));
    }

    public function amIFollowing(User $user): JsonResponse
    {
        $follow = Follow::whereUserId(Auth::user()->id)->whereFollowedId($user->id);

        if ($follow->exists()) {
            return $this->ok($follow->first());
        }

        return $this->noContent();
    }

    public function getUserFollowers(?User $user = null): JsonResponse
    {
        if (!$user) {
            $user = Auth::user();
        }

        $followers = $user->followers()->with('followers')->paginate();

        return $this->ok($followers);
    }

    public function getFollows(?User $user = null): JsonResponse
    {
        if (!$user) {
            $user = Auth::user();
        }

        $follows = $user->follows()->with('following')->paginate();

        return $this->ok($follows);
    }

    public function countFollowers(?User $user = null): JsonResponse
    {
        if (!$user) {
            $user = Auth::user();
        }

        return $this->ok(["total" => $user->followers()->count()]);
    }

    public function countFollows(?User $user = null): JsonResponse
    {
        if (!$user) {
            $user = Auth::user();
        }

        return $this->ok(['total' => $user->follows()->count()]);
    }

    public function delete(Follow $follow): JsonResponse
    {
        $follow->delete();

        return $this->noContent();
    }
}
