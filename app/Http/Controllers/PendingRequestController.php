<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendingRequest\CreatePendingRequest;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PendingRequestController extends Controller
{
    public function create(array $data, User $user)
    {
        $data['requester_id'] = Auth::user()->id;
        $data['target_id'] = $user->id;
        return PendingRequest::create($data);
    }

    public function store(CreatePendingRequest $request, User $user)
    {
        $pending = $this->create($request->validated(), $user);
        return $this->created($pending);
    }

    public function getMyPendings()
    {
        $requests = PendingRequest::whereTargetId(Auth::user()->id)->get();
        return $this->ok($requests);
    }

    public function deletePending(PendingRequest $pendingRequest)
    {
        if ($pendingRequest->target_id == Auth::user()->id) {
            PendingRequest::whereRequestId($pendingRequest->request_id)->delete();
            return $this->noContent();
        } else
            return $this->forbidden("Permission denied: Wrong user.");
    }

}
