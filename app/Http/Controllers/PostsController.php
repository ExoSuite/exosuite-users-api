<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\GetPostsRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    private function createPost(array $data)
    {
        $data['author_id'] = auth()->user()->id;
        return Post::create($data);
    }

    private function editPost(array $data)
    {
        $post = Post::whereId($data['id'])->first();
        $post->update(['content' => $data['content']]);
        return $post->globalInfos();
    }

    private function getPosts(array $data)
    {
        return Post::whereDashboardId($data['dashboard_id'])->get();
    }

    private function deletePost(array $data)
    {
        $post = Post::whereId($data['id'])->first();
        $post->delete();
    }

    public function store(CreatePostRequest $request)
    {
        $post = $this->createPost($request->validated());
        return $this->created($post);
    }

    public function update(UpdatePostRequest $request)
    {
        $post = $this->editPost($request->validated());
        return $this->ok($post);
    }

    public function getPostsFromDashboard(GetPostsRequest $request)
    {
        $posts = $this->getPosts($request->validated());
        return $this->ok($posts);
    }

    public function delete(DeletePostRequest $request)
    {
        $this->deletePost($request->validated());
        return $this->noContent();
    }
}
