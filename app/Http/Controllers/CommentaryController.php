<?php

namespace App\Http\Controllers;

use App\Http\Requests\Commentary\CreateCommentaryRequest;
use App\Http\Requests\Commentary\UpdateCommentaryRequest;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;

class CommentaryController extends Controller
{
    private function createComm(array $data, Post $post)
    {
        $data['author_id'] = Auth::user()->id;
        $data['post_id'] = $post->id;
        return Commentary::create($data);
    }

    private function updateCommentary(array $data, Commentary $commentary)
    {
        $comm = Commentary::whereId($commentary->id)->first();
        $comm->update(['content' => $data['content']]);
        return $comm;
    }

    private function getComms(Post $post)
    {
        $comms = Commentary::wherePostId($post->id)->get();
        return $this->ok($comms);
    }

    private function deleteCommentary(array $data)
    {
        $comm = Commentary::whereId($data['id'])->first();
        $comm->delete();
    }

    public function store(CreateCommentaryRequest $request, Dashboard $dashboard, Post $post)
    {
        $owner_id = $dashboard->owner_id;
        if ($owner_id !== Auth::user()->id && $post->author_id !== Auth::user()->id)
        {
            switch ($dashboard->restriction)
            {
                case Restriction::PUBLIC:{
                    return $this->created($this->createComm($request->validated(), $post));
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createComm($request->validated(), $post));
                    else
                        return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createComm($request->validated(), $post));
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists())
                        return $this->created($this->createComm($request->validated(), $post));
                    else
                        return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
            }
        }
        else
            return $this->created($this->createComm($request->validated(), $post));
    }

    public function getCommsFromPost(Dashboard $dashboard, Post $post)
    {
        $owner_id = $dashboard->owner_id;
        if ($owner_id !== Auth::user()->id)
        {
            switch ($dashboard->restriction)
            {
                case Restriction::PUBLIC:{
                    return $this->getComms($post);
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getComms($post);
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(Auth::user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getComms($post);
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', Auth::user()->id)->exists())
                        return $this->getComms($post);
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
            }
        }
        else
            return $this->getComms($post);
    }

    public function updateComm(UpdateCommentaryRequest $request, Dashboard $dashboard, Post $post, Commentary $commentary)
    {
        if ($commentary->author_id == Auth::user()->id)
        {
            $comm = $this->updateCommentary($request->validated(), $commentary);
            return $this->ok($comm);
        }
        else
            return $this->forbidden("Permission denied: You're not allow to modify this commentary.");
    }

    public function deleteComm(Dashboard $dashboard, Post $post, Commentary $commentary)
    {
        $owner = $dashboard->owner_id;
        if (Auth::user()->id == $owner
            || Auth::user()->id == $post->author_id
            || Auth::user()->id == $commentary->author_id)
        {
            Commentary::whereId($commentary->id)->delete();
            return $this->noContent();
        }
        else
            return $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }
}
