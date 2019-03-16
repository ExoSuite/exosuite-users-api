<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Dashboard;
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
        return $this->created($this->createPost($request->validated(), $dashboard->id));
    }

    public function update(UpdatePostRequest $request, User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
            return $this->ok($this->editPost($request->validated(), $post->id));
    }

    public function getPostsFromDashboard(User $user, Dashboard $dashboard): JsonResponse
    {
        return $this->ok(Post::whereDashboardId($dashboard->id)->get());
    }


    public function delete(User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        Post::whereId($post->id)->delete();

        return $this->noContent();
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
}
