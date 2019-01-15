<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return $this->ok(User::with('profile')->whereId($user->id)->first()->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserProfileRequest $request)
    {
        Auth::user()->profile()->update($request->validated());

        return $this->noContent();
    }
}
