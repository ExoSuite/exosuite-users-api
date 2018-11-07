<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PendingRequest;

class PendingRequestController extends Controller
{
    public function create(array $data)
    {
        return PendingRequest::create($data);
    }

    public function store(CreatePendingRequest $request)
    {
        $pending = $this->create($request->validated());
        return $this->created($pending);
    }

    public function getMyPendings()
    {
        return PendingRequest::whereTargetId(auth()->user()->id)->get();
    }

}
