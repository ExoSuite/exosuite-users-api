<?php declare(strict_types = 1);

namespace App\Http\Controllers\User;

use App\Enums\CollectionPicture;
use App\Enums\MediaConversion;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserProfilePictureAvatarRequest;
use App\Http\Requests\CreateUserProfilePictureCoverRequest;
use App\Http\Requests\CreateUserProfilePictureRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use function route;

/**
 * Class UserProfilePictureController
 *
 * @package App\Http\Controllers\User
 */
class UserProfilePictureController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user): JsonResponse
    {
        $pictures = $user->profile()->first()->getMedia(CollectionPicture::PICTURE);
        $urls = [];

        for ($i = 0; $i !== count($pictures); $i++) {
            array_push($urls, $pictures[$i]->getFullUrl());
        }

        return $this->ok($urls);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateUserProfilePictureRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserProfilePictureRequest $request, User $user): JsonResponse
    {
        $user->profile()->first()
            ->addMedia($request->file('picture'))
            ->preservingOriginal()
            ->toMediaCollection(CollectionPicture::PICTURE);

        return $this->noContent();
    }

    /**
     * Store a newly avatar
     *
     * @param \App\Http\Requests\CreateUserProfilePictureAvatarRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAvatar(CreateUserProfilePictureAvatarRequest $request, User $user): JsonResponse
    {
        /** @var \App\Models\UserProfile $profile */
        $profile = $user->profile()->first();
        $profile->addMedia($request->file('picture'))
            ->toMediaCollection(CollectionPicture::AVATAR);
        $profile->update([
            'avatar_id' =>
                $profile->getMedia(CollectionPicture::AVATAR)
                    ->sortByDesc('created_at')
                    ->first()->id,
        ]);

        return $this->created([], route('get_picture_avatar', ['user' => $user->id]));
    }

    /**
     * Display the avatar.
     *
     * @param \App\Models\User $user
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    public function show(User $user): StreamedResponse
    {
        /** @var \App\Models\UserProfile $profile */
        $profile = $user->profile()->first();
        $avatarId = $profile->avatar_id;

        if (!$avatarId) {
            return $this->localFile("app/default-media/avatar.png");
        }

        return $this->file(
            $profile->getMedia(CollectionPicture::AVATAR)
                ->where('id', $avatarId)
                ->last(),
            MediaConversion::THUMB
        );
    }

    /**
     * Store a newly cover
     *
     * @param \App\Http\Requests\CreateUserProfilePictureCoverRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCover(CreateUserProfilePictureCoverRequest $request, User $user): JsonResponse
    {
        $profile = $user->profile()->first();
        $profile->addMedia($request->file('picture'))
            ->toMediaCollection(CollectionPicture::COVER);
        $profile->update([
            'cover_id' =>
                $profile->getMedia(CollectionPicture::COVER)
                    ->sortByDesc('created_at')
                    ->first()->id,
        ]);

        return $this->created([], route('get_picture_cover', ['user' => $user->id]));
    }

    /**
     * Display the cover
     *
     * @param \App\Models\User $user
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    public function showCover(User $user): StreamedResponse
    {
        $profile = $user->profile()->first();
        $coverId = $profile->cover_id;

        if (!$coverId) {
            throw new UnprocessableEntityHttpException('Profile Cover id not set.');
        }

        return $this->file(
            $profile->getMedia(CollectionPicture::COVER)
                ->where('id', $coverId)
                ->last(),
            MediaConversion::BANNER
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy(int $id): void
    {
    }
}
