<?php

namespace App\Http\Controllers;

use App\Enums\RequestTypesEnum;
use App\Http\Controllers\Traits\JsonResponses;
use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

/**
 * Class RelationsController
 * @package App\Http\Controllers
 */
class RelationsController extends Controller
{
    use JsonResponses;

    /**
     * @param Uuid $id
     * @return Friendship|\Illuminate\Database\Eloquent\Model
     */
    public function createFriendship($id)
    {
        return Friendship::create(['user_id' => Auth::user()->id,
            'friend_id' => $id]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendFriendshipRequest(User $user)
    {
        $this->createFriendship($user->id);

        $request = PendingRequest::create([
            'requester_id' => Auth::user()->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $user->id
        ]);

        return $this->created($request);
    }


    /**
     * @param PendingRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     **/
    public function acceptRequest(PendingRequest $request)
    {
        if ($request->target_id == Auth::user()->id) {
            $friendship = $this->createFriendship($request->requester_id);
            $request->delete();
            return $this->ok($friendship);
        }
        return $this->forbidden("You're not allowed to answer this request");
    }

    /**
     * @param PendingRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function declineRequest(PendingRequest $request)
    {
        if ($request->target_id == Auth::user()->id) {
            $friendship = Friendship::whereFriendId($request->target_id)->whereUserId($request->requester_id);
            if ($friendship->exists()) {
                $friendship->delete();
                $request->delete();
                return $this->noContent();
            } else {
                $request->delete();
                return $this->noContent();
            }
        }
        return $this->forbidden("You're not allowed to answer this request");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyFriendships()
    {
        $friends = Friendship::whereUserId(Auth::user()->id)->get();
        return $this->ok($friends);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFriendships(User $user)
    {
        $friends = Friendship::whereUserId($user->id)->get();
        return $this->ok($friends);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteFriendships(User $user)
    {
        $friendship_link1 = Friendship::whereFriendId($user->id)->whereUserId(Auth::user()->id);
        $friendship_link2 = Friendship::whereFriendId(Auth::user()->id)->whereUserId($user->id);
        if ($friendship_link1->exists() && $friendship_link2->exists()) {
            $friendship_link1->delete();
            $friendship_link2->delete();
            return $this->noContent();
        } else
            return $this->badRequest("There is no such relation between you and this user.");
    }
}
