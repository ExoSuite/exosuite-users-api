<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRestrictionsRequest;
use App\Http\Requests\User\UserProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserProfileController
 *
 * @package App\Http\Controllers\User
 */
class UserProfileController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->ok($user->load('profile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\User\UserProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserProfileRequest $request): JsonResponse
    {
        Auth::user()->profile()->update($request->validated());

        return $this->noContent();
    }

    public function updateRestrictions(UpdateProfileRestrictionsRequest $request): JsonResponse
    {
        Auth::user()->profileRestrictions()->update($request->validated());

        return $this->noContent();
    }

    public function getProfileRestrictions(?User $user = null): JsonResponse
    {
        $user = $user ?? Auth::user();

        return $this->ok($user->profileRestrictions()->first());
    }
}
