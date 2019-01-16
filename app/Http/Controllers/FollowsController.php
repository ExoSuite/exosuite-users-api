<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFollowRequest;
use App\Models\Follow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function AmIFollowing($id)
    {
        //$data = $request->validated();
        if (Follow::whereUserId(auth()->user()->id)->whereFollowedId($id)->exists())
            return $this->ok(['status' => true]);
        else
            return $this->ok(['status' => false]);
    }

    public function WhoIsFollowing($id)
    {
        if (Follow::whereFollowedId($id)->exists())
            return $this->ok(Follow::whereFollowedId($id)->get()->pluck('user_id'));
        else
            return $this->noContent();

    }

    public function delete(CreateFollowRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id']);
        if ($entity->exists())
        {
            $entity->delete();
            return $this->noContent();
        }
        else
            return JsonResponse::HTTP_BAD_REQUEST;
    }
}
