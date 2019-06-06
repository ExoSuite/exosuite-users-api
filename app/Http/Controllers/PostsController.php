<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
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

    public function store(CreatePostRequest $request, User $user): JsonResponse
    {
        return $this->created($this->createPost($request->validated(), $user));
    }

    public function update(UpdatePostRequest $request, User $user, Post $post): JsonResponse
    {
        return $this->ok($this->editPost($request->validated(), $post));
    }

    public function getPostsFromDashboard(User $user): JsonResponse
    {
        $posts = $user->postsFromDashboard()->latest()->with('author', 'commentaries.author')->paginate();

        return $this->ok($posts);
    }


    public function delete(User $user, Post $post): JsonResponse
    {
        $post->delete();

        return $this->noContent();
    }

    /**
     * @param string[] $data
     * @param \App\Models\User $user
     * @return \App\Models\Post
     */
    private function createPost(array $data, User $user): Post
    {
        $data['author_id'] = Auth::user()->id;
        $data['dashboard_id'] = $user->dashboard->id;

        return $user->dashboard->posts()->create($data);
    }

    /**
     * @param string[] $data
     * @param \App\Models\Post $post
     * @return \App\Models\Post
     */
    private function editPost(array $data, Post $post): Post
    {
        $post->update(['content' => $data['content']]);

        return $post;
    }
}
