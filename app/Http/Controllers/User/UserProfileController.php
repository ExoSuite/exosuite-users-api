<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Enums\Preferences;
use App\Enums\Restriction;
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
        $target_id = $user->id;
        $infos = [
            'name' => "",
            'city' => $user_profile["profile"]["city"],
            'birthday' => $user_profile["profile"]["birthday"],
            'description' => $user_profile["profile"]["description"],
        ];

        $infos['name'] = $restrictions['nomination_preference'] === Preferences::NICKNAME
        && $user_profile['nick_name'] !== null
            ? $user_profile['nick_name']
            : $user_profile['first_name'] . " " . $user_profile['last_name'];

        $infos = call_user_func(
            $this->relationsValidation[$restrictions["city"]],
            $target_id,
            $infos,
            "city"
        );
        $infos = call_user_func(
            $this->relationsValidation[$restrictions["description"]],
            $target_id,
            $infos,
            "description"
        );
        $infos = call_user_func(
            $this->relationsValidation[$restrictions["birthday"]],
            $target_id,
            $infos,
            "birthday"
        );

        return $this->ok($infos);
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
        return $user === null ? $this->ok(Auth::user()->profileRestrictions()->first())
            : $this->ok($user->profileRestrictions()->first());
    }

    /**
     * @param string $target_id
     * @param array<mixed> $profile
     * @param string $field
     * @return array<mixed>
     */
    public function allowPublic(string $target_id, array $profile, string $field): array
    {
        return $profile;
    }

    /**
     * @param string $target_id
     * @param array<mixed> $profile
     * @param string $field
     * @return array<mixed>
     */
    public function checkFriendship(string $target_id, array $profile, string $field): array
    {
        $user_id = Auth::id();

        if (Friendship::whereUserId($target_id)->whereFriendId($user_id)->exists()) {
            return $profile;
        }

        $profile[$field] = null;

        return $profile;
    }

    /**
     * @param string $target_id
     * @param array<mixed> $profile
     * @param string $field
     * @return array<mixed>
     */
    public function checkFollow(string $target_id, array $profile, string $field): array
    {
        $user_id = Auth::id();

        if (Follow::whereFollowedId($target_id)->whereUserId($user_id)->exists()) {
            return $profile;
        }

        $profile[$field] = null;

        return $profile;
    }

    /**
     * @param string $target_id
     * @param array<mixed> $profile
     * @param string $field
     * @return array<mixed>
     */
    public function denyPrivate(string $target_id, array $profile, string $field): array
    {
        $profile[$field] = null;

        return $profile;
    }
}
