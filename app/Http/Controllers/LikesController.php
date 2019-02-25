<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\LikableEntities;
use App\Http\Controllers\Controller;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Like;
use App\Models\Post;
use App\Models\Run;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class LikesController
 *
 * @package App\Http\Controllers
 */
class LikesController extends Controller
{

    public function store(User $user, Dashboard $dashboard, Post $post, ?Commentary $commentary = null): JsonResponse
    {
        return $commentary !== null ? $this->createLike($commentary, LikableEntities::COMMENTARY) : $this->createLike($post, LikableEntities::POST);
    }

    private function createLike(Model $entity, string $type): JsonResponse
    {
        return $this->created(Like::create([
            'liker_id' => Auth::user()->id,
            'liked_id' => $entity->id,
            'liked_type' => $type,
        ]));
    }

    public function delete(User $user, Dashboard $dashboard, Post $post, ?Commentary $commentary = null): JsonResponse
    {
        if ($commentary !== null) {
            $like = Like::whereLikedId($commentary->id);

            if ($like->exists()) {
                $like->delete();

                return $this->noContent();
            }
        } else {
            $like = Like::whereLikedId($post->id);

            if ($like->exists()) {
                $like->delete();

                return $this->noContent();
            }
        }
    }

    public function getLikesFromID(User $user, Dashboard $dashboard, Post $post, ?Commentary $commentary = null): JsonResponse
    {
        return $commentary !== null
            ? $this->ok(Like::whereLikedId($commentary->id)->get())
            : $this->ok(Like::whereLikedId($post->id)->get());
    }

    public function getLikesFromLiker(User $user): JsonResponse
    {
        return $this->ok(Like::whereLikerId($user->id)->get());
    }

    public function storeRun(Run $run): JsonResponse
    {
        return $this->createLike($run, LikableEntities::RUN);
    }

    public function deleteRun(Run $run): JsonResponse
    {
        $like = Like::whereLikedId($run->id);

        if ($like->exists()) {
            $like->delete();

            return $this->noContent();
        }

        return $this->badRequest("Unknown entity provided.");
    }

    public function getLikesFromRun(Run $run): JsonResponse
    {
        $likes = Like::whereLikedId($run->id)->get();

        return $this->ok($likes);
    }
}
