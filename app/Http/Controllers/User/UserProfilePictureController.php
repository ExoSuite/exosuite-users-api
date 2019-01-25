<?php

namespace App\Http\Controllers\User;

use App\Enums\CollectionPicture;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserProfilePictureAvatarRequest;
use App\Http\Requests\CreateUserProfilePictureCoverRequest;
use App\Http\Requests\CreateUserProfilePictureRequest;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserProfilePictureController
 * @package App\Http\Controllers\User
 */
class UserProfilePictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $user)
    {
        $pictures = $user->profile()->first()->getMedia(CollectionPicture::PICTURE);
        $urls = array();
        for ($i = 0; $i != sizeof($pictures); $i++) {
            array_push($urls, $pictures[$i]->getFullUrl());
        }
        return $this->ok($urls);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserProfilePictureRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserProfilePictureRequest $request, User $user)
    {
        $user->profile()->first()
            ->addMedia($request->file("picture"))
            ->preservingOriginal()
            ->toMediaCollection(CollectionPicture::PICTURE);
        return $this->noContent();
    }

    /**
     * Store a newly avatar
     * @param CreateUserProfilePictureAvatarRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAvatar(CreateUserProfilePictureAvatarRequest $request, User $user)
    {
        $profile = $user->profile()->first();
        $profile->addMedia($request->file("picture"))
            ->toMediaCollection(CollectionPicture::AVATAR);
        $profile->update([
            'avatar_id' =>
                $profile->getMedia(CollectionPicture::AVATAR)
                    ->sortByDesc('created_at')
                    ->first()->id
        ]);

        return $this->created([], route('get_picture_avatar', ['user' => $user->id]));
    }

    /**
     * Display the avatar.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $profile = $user->profile()->first();
        $avatarId = $profile->avatar_id;
        if (!$avatarId) {
            throw new UnprocessableEntityHttpException("Avatar id not set.");
        }

        return $this->file(
            $profile->getMedia(CollectionPicture::AVATAR)
                ->where('id', $avatarId)
                ->last()->getPath('thumb')
        );
    }

    /**
     * Store a newly cover
     * @param CreateUserProfilePictureCoverRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCover(CreateUserProfilePictureCoverRequest $request, User $user)
    {
        $profile = $user->profile()->first();
        $profile->addMedia($request->file("picture"))
            ->toMediaCollection(CollectionPicture::COVER);
        $profile->update([
            'cover_id' =>
                $profile->getMedia(CollectionPicture::COVER)
                    ->sortByDesc('created_at')
                    ->first()->id
        ]);

        return $this->created([], route('get_picture_cover', ['user' => $user->id]));
    }

    /**
     * Display the cover
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function showCover(User $user)
    {
        $profile = $user->profile()->first();
        $coverId = $profile->cover_id;
        if (!$coverId) {
            throw new UnprocessableEntityHttpException("Profile Cover id not set.");
        }

        return $this->file(
            $profile->getMedia(CollectionPicture::COVER)
                ->where('id', $coverId)
                ->last()->getPath('banner')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
