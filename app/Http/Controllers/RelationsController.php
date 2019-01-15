<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PendingRequest;
use App\Models\Friendship;
use App\Http\Controllers\Traits\JsonResponses;
use App\Constants\RequestTypes;
use App\Http\Requests\CreateFriendshipRequest;
use App\Http\Requests\AnswerFriendshipRequest;

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
            'type' => RequestTypes::FRIENDSHIP_REQUEST,
            'target_id' => $request->get('target_id')
        ]);

        return $this->created($request);
    }

    public function acceptRequest(AnswerFriendshipRequest $request)
    {
        $data = $request->validated();
        $pending = PendingRequest::whereRequestId($data['request_id'])->first();
        $friendship = $this->createFriendship(['target_id' => $pending['requester_id']]);
        $pending->delete();
        return $this->ok($friendship);
    }

    public function declineRequest(AnswerFriendshipRequest $request)
    {
        $data = $request->validated();
        $pending = PendingRequest::whereRequestId($data['request_id'])->first();
        Friendship::whereUserId($pending['requester_id'])->whereFriendId(auth()->user()->id)->delete();
        $pending->delete();
        return $this->noContent();
    }

    public function getMyFriendships()
    {
        $friends = Friendship::whereUserId(auth()->user()->id)->get();
        return $this->ok($friends);
    }

    public function getFriendships($user_id)
    {
        $friends = Friendship::whereUserId($user_id)->get();
        return $this->ok($friends);
    }

}
