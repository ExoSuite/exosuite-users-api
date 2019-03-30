<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\LikableEntities;
use App\Models\Commentary;
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

    public function store(User $user, Post $post, ?Commentary $commentary = null): JsonResponse
    {
        if ($commentary) {
            return $this->createLike($commentary, LikableEntities::COMMENTARY);
        }

        return $this->createLike($post, LikableEntities::POST);
    }

    public function delete(User $user, Post $post, ?Commentary $commentary = null): JsonResponse
    {
        if ($commentary !== null) {
            $like = $commentary->likeFromUser();

            if ($like->exists()) {
                $like->delete();
            }
        } else {
            $like = $post->likeFromUser();

            if ($like->exists()) {
                $like->delete();
            }
        }

        return $this->noContent();
    }

    public function getLikesFromID(
        User $user,
        Post $post,
        ?Commentary $commentary = null
    ): JsonResponse
    {
        if ($commentary) {
            return $this->ok(Like::whereLikedId($commentary->id)->get());
        }

        return $this->ok(Like::whereLikedId($post->id)->get());
    }

    public function getLikesFromLiker(User $user): JsonResponse
    {
        return $this->ok(Like::whereLikerId($user->id)->get());
    }

    public function storeRun(User $user, Run $run): JsonResponse
    {
        return $this->createLike($run, LikableEntities::RUN);
    }

    public function deleteRun(User $user, Run $run): JsonResponse
    {
        $like = $run->likeFromUser();

        if ($like->exists()) {
            $like->delete();
        }

        return $this->noContent();
    }

    public function getLikesFromRun(User $user, Run $run): JsonResponse
    {
        $likes = $run->likes()->get();

        return $this->ok($likes);
    }

    private function createLike(Model $entity, string $type): JsonResponse
    {
        return $this->created(Like::create([
            'liker_id' => Auth::user()->id,
            'liked_id' => $entity->id,
            'liked_type' => $type,
        ]));
    }
}
