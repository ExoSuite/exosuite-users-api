<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFollowRequest;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function createFollow(array $data)
    {
        return Follow::create([
           'user_id' => auth()->user()->id,
            'followed_id' => $data['id']
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
        if (Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id'])->exists())
            return ['status' => true];
        else
            return ['status' => false];
    }

    public function WhoIsFollowing(CreateFollowRequest $request)
    {
        $data = $request->validated();

        if (Follow::whereFollowedId($data['id'])->exists())
            return Follow::whereFollowedId($data['id'])->get()->pluck('user_id');
    }

    public function delete(CreateFollowRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id']);
        if ($entity->exists())
            $entity->delete();
    }
}
