<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendingRequest\CreatePendingRequest;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class PendingRequestController
 * @package App\Http\Controllers
 */
class PendingRequestController extends Controller
{
    /**
     * @param array $data
     * @param User $user
     * @return PendingRequest|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data, User $user)
    {
        $data['requester_id'] = Auth::user()->id;
        $data['target_id'] = $user->id;
        return PendingRequest::create($data);
    }

    /**
     * @param CreatePendingRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePendingRequest $request, User $user)
    {
        $pending = $this->create($request->validated(), $user);
        return $this->created($pending);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyPendings()
    {
        $requests = PendingRequest::whereTargetId(Auth::user()->id)->get();
        return $this->ok($requests);
    }

    /**
     * @param PendingRequest $pendingRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deletePending(PendingRequest $pendingRequest)
    {
        if ($pendingRequest->target_id == Auth::user()->id) {
            PendingRequest::whereRequestId($pendingRequest->request_id)->delete();
            return $this->noContent();
        } else {
            return $this->forbidden("Permission denied: Wrong user.");
        }
    }
}
