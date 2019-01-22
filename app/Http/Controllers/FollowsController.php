<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFollowRequest;
use App\Http\Requests\DeleteFollowsRequest;
use App\Http\Requests\GetFollowsRequest;
use App\Models\Follow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;


class FollowsController extends Controller
{
    public function createFollow(array $data)
    {
        if (Follow::whereFollowedId($data['id'])->whereUserId(auth()->user()->id)->exists())
            return Response::json('Already following.')->setStatusCode(HttpResponse::HTTP_BAD_REQUEST);
        else
            return $this->created(Follow::create([
                'user_id' => auth()->user()->id,
                'followed_id' => $data['id']
            ]));
    }

    public function store(CreateFollowRequest $request)
    {
        $follow = $this->createFollow($request->validated());
        return $follow;
    }

    public function AmIFollowing(GetFollowsRequest $request, $id)
    {
        $request->validated();
        if (Follow::whereUserId(auth()->user()->id)->whereFollowedId($id)->exists())
            return $this->ok(['status' => true]);
        else
            return $this->ok(['status' => false]);
    }

    public function WhoIsFollowing(GetFollowsRequest $request, $id)
    {
        $request->validated();
        if (Follow::whereFollowedId($id)->exists())
            return $this->ok(Follow::whereFollowedId($id)->get()->pluck('user_id'));
        else
            return $this->noContent();

    }

    public function delete(DeleteFollowsRequest $request, $followed_id)
    {
        $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($followed_id);
        if ($entity->exists())
        {
            $entity->delete();
            return $this->noContent();
        }
        else
            return $this->badRequest("You're not following this user.");
    }
}
