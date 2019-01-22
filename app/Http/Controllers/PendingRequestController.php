<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePendingRequest;
use App\Http\Requests\DeletePendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PendingRequest;

class PendingRequestController extends Controller
{
    public function create(array $data)
    {
        $data['requester_id'] = auth()->user()->id;
        return PendingRequest::create($data);
    }

    public function store(CreatePendingRequest $request)
    {
        $pending = $this->create($request->validated());
        return $this->created($pending);
    }

    public function getMyPendings()
    {
        $requests = PendingRequest::whereTargetId(auth()->user()->id)->get();
        return $this->ok($requests);
    }

    public function deletePending(DeletePendingRequest $request, $request_id)
    {
        $request->validated();
        $my_request = PendingRequest::whereRequestId($request_id)->first();
        if ($my_request['target_id'] == auth()->user()->id)
        {
            PendingRequest::whereRequestId($request_id)->delete();
            return $this->noContent();
        }
        else
            return $this->forbidden("Wrong user.");
    }

}
