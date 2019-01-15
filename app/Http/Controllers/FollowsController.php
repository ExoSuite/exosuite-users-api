<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFollowRequest;
use App\Models\Follow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function createFollow(array $data)
    {
        return Follow::create([
           'user_id' => auth()->user()->id
        ]);
    }

    public function store(CreateFollowRequest $request)
    {
        $follow = $this->createFollow($request->validated());
        return $this->created($follow);
    }

    public function AmIFollowing(CreateFollowRequest $request)
    {
        $data = $request->validated();
        if (Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['followed_id'])->exists())
            return $this->ok(['status' => true]);
        else
            return $this->ok(['status' => false]);
    }

    public function WhoIsFollowing(CreateFollowRequest $request)
    {
        $data = $request->validated();

        if (Follow::whereFollowedId($data['followed_id'])->exists())
            return $this->ok(Follow::whereFollowedId($data['followed_id'])->get()->pluck('user_id'));
        else
            return $this->noContent();

    }

    public function delete(CreateFollowRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['followed_id']);
        if ($entity->exists())
        {
            $entity->delete();
            return $this->noContent();
        }
        else
            return JsonResponse::HTTP_BAD_REQUEST;
    }
}
