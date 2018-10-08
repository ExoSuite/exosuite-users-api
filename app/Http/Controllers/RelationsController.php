<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Models\PendingRequest;
use App\Models\TMPFriendship;
use App\Http\Controllers\Traits\JsonResponses;
use RequestTypes;

class RelationsController extends Controller
{
    use JsonResponses;

    public function sendFriendshipRequest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "target_id" => "required|exists:users"
        ]);
        if ($validation->fails())
            return $validation->errors();

        PendingRequests::create([
            'requester_id' => auth()->user()->user_id,
            'type' => RequestTypes::FRIENDSHIP_REQUEST,
            'target_id' => $request->get('target_id')
        ]);
        TMPFriendship::create([
            'user_id' => auth()->user()->user_id,
            'friend_id' => $request->get('target_id')
        ]);

        return $this->created();
    }

    public function acceptRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:pending_requests'
        ]);
        if ($validator->fails())
            return $validator->errors();
        $pending = PendingRequest::whereRequestId($request->get('request_id'))->first();
        TMPFriendship::create([
            'user_id' => auth()->user()->user_id,
            'friend_id' => $pending['requester_id']
        ]);

        return $this->created();
    }

    public function declineRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:pending_requests'
        ]);
        if ($validator->fails())
            return $validator->errors();
        $pending = PendingRequest::whereRequestId($request->get('request_id'))->first();
        TMPFriendship::whereUserId($request['requester_id'])->whereTargetId(auth()->user()->user_id)->delete();
        $pending->delete();
    }

    public function getMyFriendships()
    {
        return TMPFriendship::whereUserId(auth()->user()->user_id)->get();
    }

    public function getFriendships($user_id)
    {
        return TMPFriendship::whereUserId($user_id)->get();
    }

}
