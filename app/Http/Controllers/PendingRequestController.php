<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\PendingRequest\CreatePendingRequest;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class PendingRequestController
 *
 * @package App\Http\Controllers
 */
class PendingRequestController extends Controller
{

    public function store(CreatePendingRequest $request, User $user): JsonResponse
    {
        $pending = $this->create($request->validated(), $user);

        return $this->created($pending);
    }

    /**
     * @param string[] $data
     * @param \App\Models\User $user
     * @return \App\Models\PendingRequest
     */
    public function create(array $data, User $user): PendingRequest
    {
        $data['requester_id'] = Auth::user()->id;
        $data['target_id'] = $user->id;

        return PendingRequest::create($data);
    }

    public function getMyPendings(): JsonResponse
    {
        $me = Auth::user();

        $requests = $me->pendingRequests("target_id")->with('user')->paginate();

        return $this->ok($requests);
    }

    public function deletePending(PendingRequest $pendingRequest): JsonResponse
    {
        $pendingRequest->delete();

        return $this->noContent();
    }
}
