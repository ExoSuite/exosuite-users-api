<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\Commentary\CreateCommentaryRequest;
use App\Http\Requests\Commentary\UpdateCommentaryRequest;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class CommentaryController
 *
 * @package App\Http\Controllers
 */
class CommentaryController extends Controller
{

    public function store(CreateCommentaryRequest $request, User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        $owner_id = $dashboard->owner_id;

        if ($owner_id === Auth::user()->id || $post->author_id === Auth::user()->id) {
            return $this->created($this->createComm($request->validated(), $post));
        }

        switch ($dashboard->restriction) {
            case Restriction::PUBLIC:
                return $this->created($this->createComm($request->validated(), $post));
            case Restriction::FRIENDS:
                return Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()
                    ? $this->created($this->createComm($request->validated(), $post))
                    : $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
            case Restriction::FRIENDS_FOLLOWERS:
                if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()) {
                    return $this->created($this->createComm($request->validated(), $post));
                }

                return Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists()
                    ? $this->created($this->createComm($request->validated(), $post))
                    : $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
            default:
                return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
        }
    }

    public function getCommsFromPost(User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        $owner_id = $dashboard->owner_id;

        if ($owner_id === Auth::user()->id) {
            return $this->getComms($post);
        }

        switch ($dashboard->restriction) {
            case Restriction::PUBLIC:
                return $this->getComms($post);
            case Restriction::FRIENDS:
                return Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()
                    ? $this->getComms($post)
                    : $this->forbidden("Permission denied: You're not allowed to access this post.");
            case Restriction::FRIENDS_FOLLOWERS:
                if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()) {
                    return $this->getComms($post);
                }

                return Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists()
                    ? $this->getComms($post)
                    : $this->forbidden("Permission denied: You're not allowed to access this post.");
            default:
                return $this->forbidden("Permission denied: You're not allowed to access this post.");
        }
    }

    public function updateComm(
        UpdateCommentaryRequest $request,
        User $user,
        Dashboard $dashboard,
        Post $post,
        Commentary $commentary
    ): JsonResponse
    {
        if ($commentary->author_id === Auth::user()->id) {
            $comm = $this->updateCommentary($request->validated(), $commentary);

            return $this->ok($comm);
        }

        return $this->forbidden("Permission denied: You're not allow to modify this commentary.");
    }

    public function deleteComm(User $user, Dashboard $dashboard, Post $post, Commentary $commentary): JsonResponse
    {
        $owner = $dashboard->owner_id;

        if (Auth::user()->id === $owner
            || Auth::user()->id === $post->author_id
            || Auth::user()->id === $commentary->author_id) {
            Commentary::whereId($commentary->id)->delete();

            return $this->noContent();
        }

        return $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }

    /**
     * @param string[] $data
     * @param \App\Models\Post $post
     * @return \App\Models\Commentary
     */
    private function createComm(array $data, Post $post): Commentary
    {
        $data['author_id'] = Auth::user()->id;
        $data['post_id'] = $post->id;

        return Commentary::create($data);
    }

    private function getComms(Post $post): JsonResponse
    {
        $comms = Commentary::wherePostId($post->id)->get();

        return $this->ok($comms);
    }

    /**
     * @param string[] $data
     * @param \App\Models\Commentary $commentary
     * @return \App\Models\Commentary
     */
    private function updateCommentary(array $data, Commentary $commentary): Commentary
    {
        $comm = Commentary::whereId($commentary->id)->first();
        $comm->update(['content' => $data['content']]);

        return $comm;
    }

    /*
    /**
     * @param string[] $data
     * @throws \Exception
    private function deleteCommentary(array $data): void
    {
        $comm = Commentary::whereId($data['id'])->first();
        $comm->delete();
    }
    */
}
