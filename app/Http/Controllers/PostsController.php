<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\Restriction;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class PostsController
 *
 * @package App\Http\Controllers
 */
class PostsController extends Controller
{

    public function store(CreatePostRequest $request, User $user, Dashboard $dashboard): JsonResponse
    {
        $owner_id = $dashboard->owner_id;

        if ($owner_id === Auth::user()->id) {
            return $this->created($this->createPost($request->validated(), $dashboard->id));
        }

        switch ($dashboard->restriction) {
            case Restriction::PUBLIC:
                return $this->created($this->createPost($request->validated(), $dashboard->id));
            case Restriction::FRIENDS:
                return Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()
                    ? $this->created($this->createPost($request->validated(), $dashboard->id))
                    : $this->forbidden("Permission denied: You're not authorized to post on this board.");
            case Restriction::FRIENDS_FOLLOWERS:
                if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()) {
                    return $this->created($this->createPost($request->validated(), $dashboard->id));
                }

                return Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists()
                    ? $this->created($this->createPost($request->validated(), $dashboard->id))
                    : $this->forbidden("Permission denied: You're not authorized to post on this board.");
            default:
                return $this->forbidden("Permission denied: You're not authorized to post on this board.");
        }
    }

    public function update(UpdatePostRequest $request, User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        if ($post->author_id === Auth::user()->id) {
            $post = $this->editPost($request->validated(), $post->id);

            return $this->ok($post);
        }

        return $this->forbidden("Permission denied: You're not allowed to update this post.");
    }

    public function getPostsFromDashboard(User $user, Dashboard $dashboard): JsonResponse
    {
        $owner_id = $dashboard->owner_id;

        if ($owner_id === Auth::user()->id) {
            return $this->getPosts($dashboard);
        }

        switch ($dashboard->restriction) {
            case Restriction::PUBLIC:
                return $this->getPosts($dashboard);
            case Restriction::FRIENDS:
                return Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()
                    ? $this->getPosts($dashboard)
                    : $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
            case Restriction::FRIENDS_FOLLOWERS:
                if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists()) {
                    return $this->getPosts($dashboard);
                }

                return Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists()
                    ? $this->getPosts($dashboard)
                    : $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
            default:
                return $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
        }
    }


    public function delete(User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        $owner_id = $dashboard->owner_id;

        return $post->author_id === Auth::user()->id || Auth::user()->id === $owner_id
            ? $this->deletePost($post)
            : $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }

    /**
     * @param string[] $data
     * @param mixed $dashboard_id
     * @return \App\Models\Post
     */
    private function createPost(array $data, $dashboard_id): Post
    {
        $data['author_id'] = Auth::user()->id;
        $data['dashboard_id'] = $dashboard_id;

        return Post::create($data);
    }

    /**
     * @param string[] $data
     * @param string $post_id
     * @return \App\Models\Post
     */
    private function editPost(array $data, string $post_id): Post
    {
        $post = Post::whereId($post_id)->first();
        $post->update(['content' => $data['content']]);

        return $post;
    }

    private function getPosts(Dashboard $dashboard): JsonResponse
    {
        $posts = Post::whereDashboardId($dashboard->id)->get();

        return $this->ok($posts);
    }


    private function deletePost(Post $post): JsonResponse
    {
        Post::whereId($post->id)->delete();

        return $this->noContent();
    }
}
