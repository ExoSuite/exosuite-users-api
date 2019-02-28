<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\RequestTypesEnum;
use App\Http\Controllers\Traits\JsonResponses;
use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class RelationsController
 *
 * @package App\Http\Controllers
 */
class RelationsController extends Controller
{
    use JsonResponses;

    public function sendFriendshipRequest(User $user): JsonResponse
    {
        $this->createFriendship($user->id);

        $request = PendingRequest::create([
            'requester_id' => Auth::user()->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $user->id,
        ]);

        return $this->created($request);
    }

    /**
     * @param string $id
     * @return \App\Models\Friendship|\Illuminate\Database\Eloquent\Model
     */
    public function createFriendship(string $id)
    {
        return Friendship::create(['user_id' => Auth::user()->id,
            'friend_id' => $id]);
    }

    public function acceptRequest(PendingRequest $request): JsonResponse
    {
        if ($request->target_id === Auth::user()->id) {
            $friendship = $this->createFriendship($request->requester_id);
            $request->delete();

            return $this->ok($friendship);
        }

        return $this->forbidden("You're not allowed to answer this request");
    }

    public function declineRequest(PendingRequest $request): JsonResponse
    {
        if ($request->target_id === Auth::user()->id) {
            $friendship = Friendship::whereFriendId($request->target_id)->whereUserId($request->requester_id);

            if ($friendship->exists()) {
                $friendship->delete();
                $request->delete();

                return $this->noContent();
            }

            $request->delete();

            return $this->noContent();
        }

        return $this->forbidden("You're not allowed to answer this request");
    }

    public function getMyFriendships(): JsonResponse
    {
        $friends = Friendship::whereUserId(Auth::user()->id)->get();

        return $this->ok($friends);
    }

    public function getFriendships(User $user): JsonResponse
    {
        $friends = Friendship::whereUserId($user->id)->get();

        return $this->ok($friends);
    }

    public function deleteFriendships(User $user): JsonResponse
    {
        $friendship_link1 = Friendship::whereFriendId($user->id)->whereUserId(Auth::user()->id);
        $friendship_link2 = Friendship::whereFriendId(Auth::user()->id)->whereUserId($user->id);

        if ($friendship_link1->exists() && $friendship_link2->exists()) {
            $friendship_link1->delete();
            $friendship_link2->delete();

            return $this->noContent();
        }

        return $this->badRequest('There is no such relation between you and this user.');
    }
}
