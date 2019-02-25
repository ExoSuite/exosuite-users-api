<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
     * @param array $data
     * @param \App\Models\User $user
     * @return \App\Models\PendingRequest|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data, User $user)
    {
        $data['requester_id'] = Auth::user()->id;
        $data['target_id'] = $user->id;

        return PendingRequest::create($data);
    }

    public function getMyPendings(): JsonResponse
    {
        $requests = PendingRequest::whereTargetId(Auth::user()->id)->get();

        return $this->ok($requests);
    }

    public function deletePending(PendingRequest $pendingRequest): JsonResponse
    {
        if ($pendingRequest->target_id === Auth::user()->id) {
            PendingRequest::whereRequestId($pendingRequest->request_id)->delete();

            return $this->noContent();
        }

        return $this->forbidden("Permission denied: Wrong user.");
    }
}
