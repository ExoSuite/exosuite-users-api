<?php

namespace App\Http\Controllers;

use App\Enums\LikableEntities;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Like;
use App\Models\Post;
use App\Models\Run;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    /**
     * @param Model $entity
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    private function createLike($entity, string $type)
    {
        return $this->created(Like::create([
            'liker_id' => Auth::user()->id,
            'liked_id' => $entity->id,
            'liked_type' => $type
        ]));
    }

    /**
     * @param User $user
     * @param Dashboard $dashboard
     * @param Post $post
     * @param Commentary|null $commentary
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user, Dashboard $dashboard, Post $post, Commentary $commentary = null)
    {
        if ($commentary != null)
            return $this->createLike($commentary, LikableEntities::COMMENTARY);
        else
            return $this->createLike($post, LikableEntities::POST);
    }

    /**
     * @param User $user
     * @param Dashboard $dashboard
     * @param Post $post
     * @param Commentary|null $commentary
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(User $user, Dashboard $dashboard, Post $post, Commentary $commentary = null)
    {
        if ($commentary != null)
        {
            $like = Like::whereLikedId($commentary->id);
            if ($like->exists())
            {
                $like->delete();
                return $this->noContent();
            }
        }
        else
            {
            $like = Like::whereLikedId($post->id);
            if ($like->exists())
            {
                $like->delete();
                return $this->noContent();
            }
        }
    }

    /**
     * @param User $user
     * @param Dashboard $dashboard
     * @param Post $post
     * @param Commentary|null $commentary
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLikesFromID(User $user, Dashboard $dashboard, Post $post, Commentary $commentary = null)
    {
        if ($commentary != null)
            return $this->ok(Like::whereLikedId($commentary->id)->get());
        else
            return $this->ok(Like::whereLikedId($post->id)->get());
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLikesFromLiker(User $user)
    {
        return $this->ok(Like::whereLikerId($user->id)->get());
    }

    /**
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRun(Run $run)
    {
        return $this->createLike($run, LikableEntities::RUN);
    }

    /**
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteRun(Run $run)
    {
        $like = Like::whereLikedId($run->id);
        if ($like->exists())
        {
            $like->delete();
            return $this->noContent();
        }
        else
            return $this->badRequest("Unknown entity provided.");
    }

    /**
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLikesFromRun(Run $run)
    {
        $likes = Like::whereLikedId($run->id)->get();
        return $this->ok($likes);
    }
}
