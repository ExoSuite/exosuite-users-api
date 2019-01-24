<?php

namespace App\Http\Controllers;

use App\Http\Requests\Like\CreateLikeRequest;
use App\Http\Requests\Like\DeleteLikeRequest;
use App\Http\Requests\Like\GetLikesFromIdRequest;
use App\Models\Like;
use App\Models\User;

class LikesController extends Controller
{
    private function createLike(array $data)
    {
        return $this->created(Like::create([
            'liker_id' => auth()->user()->id,
            'liked_id' => $data['entity_id'],
            'liked_type' => $data['entity_type']
        ]));
    }

    public function store(CreateLikeRequest $request)
    {
        return $this->createLike($request->validated());
    }

    public function delete(DeleteLikeRequest $request, $entity_id)
    {
        $request->validated();
        $like = Like::whereLikedId($entity_id);
        if ($like->exists())
        {
            $like->delete();
            return $this->noContent();
        }
    }

    public function getLikesFromID(GetLikesFromIdRequest $request, $entity_id)
    {
        $request->validated();
        $likes = Like::whereLikedId($entity_id)->get();
        return $this->ok($likes);
    }

    public function getLikesFromLiker(User $user)
    {
        return $this->ok(Like::whereLikerId($user->id)->get());
    }
}
