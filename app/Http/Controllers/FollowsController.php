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
        $status = Follow::whereUserId(Auth::user()->id)->whereFollowedId($user->id)->exists();

        return $this->ok(['status' => $status]);
    }

    public function getUserFollowers(User $user): JsonResponse
    {
        $followers = $user->followers()->paginate();

        foreach ($followers->items() as $follow) {
            $user_attached = User::whereId($follow['user_id'])->first();
            $follow['first_name'] = $user_attached->first_name;
            $follow['last_name'] = $user_attached->last_name;
        }

        return $this->ok($followers);
    }

    public function countFollowers(User $user): JsonResponse
    {
        return $this->ok(["total" => $user->followers()->count()]);
    }

    public function delete(Follow $follow): JsonResponse
    {
        $follow->delete();

        return $this->noContent();
    }
}
