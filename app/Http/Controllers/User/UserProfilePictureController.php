<?php

namespace App\Http\Controllers\User;

use App\Enums\CollectionPicture;
use App\Http\Requests\CreateUserProfilePictureAvatarRequest;
use App\Http\Requests\CreateUserProfilePictureCoverRequest;
use App\Http\Requests\CreateUserProfilePictureRequest;
use App\Models\User;
use App\Http\Controllers\Controller;

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
        for ($i = 0; $i != sizeof($pictures); $i++)
            array_push($urls, $pictures[$i]->getFullUrl());
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
        $user->profile()->first()
            ->addMedia($request->file("picture"))
            ->toMediaCollection(CollectionPicture::AVATAR);
        $user->profile()->first()
            ->update([
                'avatar_id' =>
                    $user->profile()
                        ->first()
                        ->getMedia(CollectionPicture::AVATAR)
                        ->sortByDesc('created_at')
                        ->first()->id
            ]);
        return $this->noContent();
    }

    /**
     * Display the avatar.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if ($user->profile()->first()->avatar_id == null)
            return response('', 422);
        return $this->file($user->profile()
            ->first()
            ->getMedia(CollectionPicture::AVATAR)
            ->where('id', $user->profile()->first()->avatar_id)
            ->last()->getPath('thumb'));
    }

    /**
     * Store a newly cover
     * @param CreateUserProfilePictureCoverRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCover(CreateUserProfilePictureCoverRequest $request, User $user)
    {
        $user->profile()->first()
            ->addMedia($request->file("picture"))
            ->toMediaCollection(CollectionPicture::COVER);
        $user->profile()->first()
            ->update([
                'cover_id' =>
                    $user->profile()
                        ->first()
                        ->getMedia(CollectionPicture::COVER)
                        ->sortByDesc('created_at')
                        ->first()->id
            ]);
        return $this->noContent();
    }

    /**
     * Display the cover
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function showCover(User $user)
    {
        if ($user->profile()->first()->cover_id == null)
            return response('', 422);
        else
            return $this->file($user->profile()
        ->first()
        ->getMedia(CollectionPicture::COVER)
        ->where('id', $user->profile()->first()->cover_id)
        ->last()->getPath('banner'));
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
