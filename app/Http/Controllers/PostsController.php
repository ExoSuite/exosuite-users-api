<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\GetPostsRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Dashboard;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;
use App\Models\Post;

class PostsController extends Controller
{
    private function deletePost(array $data)
    {
        Post::whereId($data['post_id'])->delete();
        return $this->noContent();
    }

    private function getPosts(array $data)
    {
        $posts = Post::whereDashboardId($data['dashboard_id'])->get();
        return $this->ok($posts);
    }

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

    public function store(CreatePostRequest $request)
    {
        $dashboard = Dashboard::whereId($request->get('dashboard_id'))->first();
        $owner_id = $dashboard['owner_id'];
        if ($owner_id !== auth()->user()->id)
        {
            switch ($dashboard['restriction'])
            {
                case Restriction::PUBLIC:{
                    return $this->created($this->createPost($request->validated()));
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createPost($request->validated()));
                    else
                        return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createPost($request->validated()));
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', auth()->user()->id)->exists())
                        return $this->created($this->createPost($request->validated()));
                    else
                        return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
            }
        }
        else
        {
            $post = $this->createPost($request->validated());
            return $this->created($post);
        }
    }

    public function update(UpdatePostRequest $request)
    {
        $post = Post::whereId($request->get('id'))->first();
        if ($post['author_id'] == auth()->user()->id)
        {
            $post = $this->editPost($request->validated());
            return $this->ok($post);
        }
        else
            return $this->forbidden("Permission denied: You're not allowed to update this post.");
    }

    public function getPostsFromDashboard(GetPostsRequest $request, $dashboard_id)
    {
        $dashboard = Dashboard::whereId($dashboard_id)->first();
        $owner_id = $dashboard['owner_id'];
        if ($owner_id !== auth()->user()->id)
        {
            switch ($dashboard['restriction'])
            {
                case Restriction::PUBLIC:{
                    return $this->getPosts($request->validated());
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getPosts($request->validated());
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getPosts($request->validated());
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', auth()->user()->id)->exists())
                        return $this->getPosts($request->validated());
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
                }
            }
        }
        else
        {
            $request->validated();
            $posts = Post::whereDashboardId($dashboard_id)->get();
            return $this->ok($posts);
        }
    }

    public function delete(DeletePostRequest $request, $post_id)
    {
        $post = Post::whereId($post_id)->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner_id = $dashboard['owner_id'];
        if ($post['author_id'] == auth()->user()->id || auth()->user()->id == $owner_id)
            return $this->deletePost($request->validated());
        else
            return $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }
}
