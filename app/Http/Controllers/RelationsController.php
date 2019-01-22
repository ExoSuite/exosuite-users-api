<?php

namespace App\Http\Controllers;

use App\Models\PendingRequest;
use App\Models\Friendship;
use App\Http\Controllers\Traits\JsonResponses;
use App\Enums\RequestTypesEnum;
use App\Http\Requests\CreateFriendshipRequest;
use App\Http\Requests\AnswerFriendshipRequest;
use App\Http\Requests\GetFriendshipsRequest;
use App\Http\Requests\DeleteFriendshipRequest;

class RelationsController extends Controller
{
    use JsonResponses;

    public function createFriendship(array $data)
    {
        return Friendship::create(['user_id' => auth()->user()->id,
            'friend_id' => $data['target_id']]);
    }


    public function sendFriendshipRequest(CreateFriendshipRequest $request)
    {
        $this->createFriendship($request->validated());

        $request = PendingRequest::create([
            'requester_id' => auth()->user()->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $request->get('target_id')
        ]);

        return $this->created($request);
    }

    public function acceptRequest(AnswerFriendshipRequest $request)
    {
        $data = $request->validated();
        $pending = PendingRequest::whereRequestId($data['request_id'])->first();
        if ($pending['target_id'] == auth()->user()->id)
        {
            $friendship = $this->createFriendship(['target_id' => $pending['requester_id']]);
            $pending->delete();
            return $this->ok($friendship);
        }
        else
            return $this->forbidden("You're not allowed to answer this request");
    }

    public function declineRequest(AnswerFriendshipRequest $request)
    {
        $data = $request->validated();
        $pending = PendingRequest::whereRequestId($data['request_id'])->first();
        if ($pending['target_id'] == auth()->user()->id)
        {
            Friendship::whereUserId($pending['requester_id'])->whereFriendId(auth()->user()->id)->delete();
            $pending->delete();
            return $this->noContent();
        }
        else
            return $this->forbidden("You're not allowed to answer this request");
    }

    public function getMyFriendships()
    {
        $friends = Friendship::whereUserId(auth()->user()->id)->get();
        return $this->ok($friends);
    }

    /**
     * @param GetFriendshipsRequest $request
     * @param Uuid $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFriendships(GetFriendshipsRequest $request, $user_id)
    {
        $request->validated();
        $friends = Friendship::whereUserId($user_id)->get();
        return $this->ok($friends);
    }

    public function deleteFriendships(DeleteFriendshipRequest $request, $target_id)
    {
        $request->validated();
        $friendship_link1 = Friendship::whereFriendId($target_id)->whereUserId(auth()->user()->id);
        $friendship_link2 = Friendship::whereFriendId(auth()->user()->id)->whereUserId($target_id);
        if ($friendship_link1->exists() && $friendship_link2->exists())
        {
            $friendship_link1->delete();
            $friendship_link2->delete();
            return $this->noContent();
        }
        else
            return $this->badRequest("There is no such relation between you and this user.");
    }
}
