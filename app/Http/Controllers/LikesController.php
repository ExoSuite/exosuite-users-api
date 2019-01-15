<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLikeRequest;
use App\Http\Requests\DeleteLikeRequest;
use App\Http\Requests\GetLikesFromIdRequest;
use App\Http\Requests\GetLikesFromLikerRequest;
use App\Models\Like;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    private function createLike(array $data)
    {
        $data["liker_id"] = auth()->user()->id;
        return Like::create($data);
    }

    private function deleteLike(array $data)
    {
        $like = Like::whereLikedId($data['liked_id'])->whereLikerId(auth()->user()->id)->first();
        $like->delete();
    }

    private function getLikesId(array $data)
    {
        return Like::whereLikedId($data['liked_id'])->get();
    }

    private function getLikersLikes(array $data)
    {
        return Like::whereLikerId($data['liker_id']);
    }

    public function store(CreateLikeRequest $request)
    {
        $like = $this->createLike($request->validated());
        return $this->created($like);
    }

    public function delete(DeleteLikeRequest $request)
    {
        $this->deleteLike($request->validated());
        return $this->noContent();
    }

    public function getLikesFromID(GetLikesFromIdRequest $request)
    {
        $likes = $this->getLikesId($request->validated());
        return $this->ok($likes);
    }

    public function getLikesFromLiker(GetLikesFromLikerRequest $request)
    {
        $likes = $this->getLikersLikes($request->validated());
        return $this->ok($likes);
    }
}
