<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PendingRequest;

class PendingRequestController extends Controller
{
    public function createPendingRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'target_id' => 'required|exists:users'
        ]);
        if ($validator->fails())
            return $validator->errors();
        PendingRequest::create($request->all());
    }

    public function getMyPendings()
    {
        return PendingRequest::whereTargetId(auth()->user()->user_id)->get();
    }

}
