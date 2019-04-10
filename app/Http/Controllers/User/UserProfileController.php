<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Enums\Preferences;
use App\Enums\Restriction;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRestrictionsRequest;
use App\Http\Requests\User\UserProfileRequest;
use App\Models\Follow;
use App\Models\Friendship;
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

    /** @var array<mixed> */
    protected $relationsValidation;

    public function __construct()
    {
        $this->relationsValidation = [
            Restriction::PUBLIC => [$this, 'allowPublic'],
            Restriction::FRIENDS_FOLLOWERS => [$this, 'checkFollow'],
            Restriction::FRIENDS => [$this, 'checkFriendship'],
            Restriction::PRIVATE => [$this, 'denyPrivate'],
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $restrictions = $user->profileRestrictions()->first();
        $user_profile = $user->load('profile');

        if (Auth::user()->inRole(Roles::ADMINISTRATOR)) {
            return $this->ok($user_profile);
        }

        $target_id = $user->id;

        if ($restrictions['nomination_preference'] === Preferences::NICKNAME && $user_profile['nick_name'] !== null) {
            $user_profile['first_name'] = null;
            $user_profile['last_name'] = null;
        } else {
            $user_profile['nick_name'] = null;
        }

        $fields = ['city', 'description', 'birthday'];

        foreach ($fields as $field) {
            $user_profile = call_user_func(
                $this->relationsValidation[$restrictions[$field]],
                $target_id,
                $user_profile,
                $field
            );
        }

        return $this->ok($user_profile);
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

    public function allowPublic(string $target_id, User $profile, string $field): User
    {
        return $profile;
    }

    public function checkFriendship(string $target_id, User $profile, string $field): User
    {
        $user_id = Auth::id();

        if (Friendship::whereUserId($target_id)->whereFriendId($user_id)->exists()) {
            return $profile;
        }

        $profile['profile'][$field] = null;

        return $profile;
    }

    public function checkFollow(string $target_id, User $profile, string $field): User
    {
        $user_id = Auth::id();

        if (Follow::whereFollowedId($target_id)->whereUserId($user_id)->exists()) {
            return $profile;
        }

        $profile['profile'][$field] = null;

        return $profile;
    }

    public function denyPrivate(string $target_id, User $profile, string $field): User
    {
        $profile['profile'][$field] = null;

        return $profile;
    }
}
