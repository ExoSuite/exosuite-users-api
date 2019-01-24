<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Dashboard;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    private function deletePost(Post $post)
    {
        Post::whereId($post->id)->delete();
        return $this->noContent();
    }

    private function getPosts(Dashboard $dashboard)
    {
        $posts = Post::whereDashboardId($dashboard->id)->get();
        return $this->ok($posts);
    }

    private function createPost(array $data, $dashboard_id)
    {
        $data['author_id'] = Auth::user()->id;
        $data['dashboard_id'] = $dashboard_id;
        return Post::create($data);
    }

    private function editPost(array $data, $post_id)
    {
        $post = Post::whereId($post_id)->first();
        $post->update(['content' => $data['content']]);
        return $post;
    }

    public function store(CreatePostRequest $request, User $user, Dashboard $dashboard)
    {
        $owner_id = $dashboard->owner_id;
        if ($owner_id !== Auth::user()->id)
        {
            switch ($dashboard->restriction)
            {
                case Restriction::PUBLIC:{
                    return $this->created($this->createPost($request->validated(), $dashboard->id));
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createPost($request->validated(), $dashboard->id));
                    else
                        return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createPost($request->validated(), $dashboard->id));
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists())
                        return $this->created($this->createPost($request->validated(), $dashboard->id));
                    else
                        return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not authorized to post on this board.");
                }
            }
        }
        else
            return $this->created($this->createPost($request->validated(), $dashboard->id));
    }

    public function update(UpdatePostRequest $request, User $user, Dashboard $dashboard, Post $post)
    {
        if ($post->author_id == Auth::user()->id)
        {
            $post = $this->editPost($request->validated(), $post->id);
            return $this->ok($post);
        }
        else
            return $this->forbidden("Permission denied: You're not allowed to update this post.");
    }

    public function getPostsFromDashboard(User $user, Dashboard $dashboard)
    {
        $owner_id = $dashboard->owner_id;
        if ($owner_id !== Auth::user()->id)
        {
            switch ($dashboard->restriction)
            {
                case Restriction::PUBLIC:{
                    return $this->getPosts($dashboard);
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getPosts($dashboard);
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this dashboard.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getPosts($dashboard);
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists())
                        return $this->getPosts($dashboard);
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
            return $this->getPosts($dashboard);
        }
    }

    public function delete(User $user, Dashboard $dashboard, Post $post)
    {
        $owner_id = $dashboard->owner_id;
        if ($post->author_id == Auth::user()->id || Auth::user()->id == $owner_id)
            return $this->deletePost($post);
        else
            return $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }
}
