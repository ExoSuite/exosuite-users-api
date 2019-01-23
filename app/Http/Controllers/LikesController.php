<?php

namespace App\Http\Controllers;

use App\Http\Requests\Like\CreateLikeRequest;
use App\Http\Requests\Like\DeleteLikeRequest;
use App\Http\Requests\Like\GetLikesFromIdRequest;
use App\Http\Requests\Like\GetLikesFromLikerRequest;
use App\Models\Like;

class LikesController extends Controller
{
    private function createLike(array $data)
    {
        $data["liker_id"] = auth()->user()->id;
        return Like::create($data);
    }

    public function store(CreateLikeRequest $request)
    {
        $like = $this->createLike($request->validated());
        return $this->created($like);
    }

    public function delete(DeleteLikeRequest $request, $entity_id)
    {
        $request->validated();
        Like::whereLikedId($entity_id)->whereLikerId(auth()->user()->id)->delete();
        return $this->noContent();
    }

    public function getLikesFromID(GetLikesFromIdRequest $request, $entity_id)
    {
        $request->validated();
        $likes = Like::whereLikedId($entity_id)->get();
        return $this->ok($likes);
    }

    public function getLikesFromLiker(GetLikesFromLikerRequest $request, $user_id)
    {
        $request->validated();
        $likes = Like::whereLikerId($user_id)->get();
        return $this->ok($likes);
    }
}
