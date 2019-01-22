<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentaryRequest;
use App\Http\Requests\DeleteCommentaryRequest;
use App\Http\Requests\GetCommentariesRequest;
use App\Http\Requests\UpdateCommentaryRequest;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;

class CommsController extends Controller
{
    private function createComm(array $data)
    {
        $data['author_id'] = auth()->user()->id;
        return Commentary::create($data);
    }

    private function updateCommentary(array $data)
    {
        $comm = Commentary::whereId($data['id'])->first();
        $comm->update(['content' => $data['content']]);
        return $comm->globalInfos();
    }

    private function getComms(array $data)
    {
        $comms = Commentary::wherePostId($data['post_id'])->get();
        return $this->ok($comms);
    }

    private function deleteCommentary(array $data)
    {
        $comm = Commentary::whereId($data['id'])->first();
        $comm->delete();
    }

    public function store(CreateCommentaryRequest $request)
    {
        $post = Post::whereId($request->get('post_id'))->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner_id = $dashboard['owner_id'];
        if ($owner_id !== auth()->user()->id && $post['author_id'] !== auth()->user()->id)
        {
            switch ($dashboard['restriction'])
            {
                case Restriction::PUBLIC:{
                    return $this->created($this->createComm($request->validated()));
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createComm($request->validated()));
                    else
                        return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->created($this->createComm($request->validated()));
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', auth()->user()->id)->exists())
                        return $this->created($this->createComm($request->validated()));
                    else
                        return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not allowed to post a commentary on this post");
                }
            }
        }
        else
            return $this->created($this->createComm($request->validated()));
    }

    public function getCommsFromPost(GetCommentariesRequest $request, $post_id)
    {
        $post = Post::whereId($post_id)->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner_id = $dashboard['owner_id'];
        if ($owner_id !== auth()->user()->id)
        {
            switch ($dashboard['restriction'])
            {
                case Restriction::PUBLIC:{
                    return $this->getComms($request->validated());
                }
                case Restriction::FRIENDS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getComms($request->validated());
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
                case Restriction::FRIENDS_FOLLOWERS:{
                    if (Friendship::whereUserId(auth()->user()->id)->where('friend_id', $owner_id)->exists())
                        return $this->getComms($request->validated());
                    elseif (Follow::whereFollowedId($owner_id)->where('user_id', auth()->user()->id)->exists())
                        return $this->getComms($request->validated());
                    else
                        return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
                default:{
                    return $this->forbidden("Permission denied: You're not allowed to access this post.");
                }
            }
        }
        else
            return $this->getComms($request->validated());
    }

    public function updateComm(UpdateCommentaryRequest $request)
    {
        $comm = Commentary::whereId($request->get('id'))->first();
        if ($comm['author_id'] == auth()->user()->id)
        {
            $comm = $this->updateCommentary($request->validated());
            return $this->ok($comm);
        }
        else
            return $this->forbidden("Permission denied: You're not allow to modify this commentary.");
    }

    public function deleteComm(DeleteCommentaryRequest $request, $comm_id)
    {
        $request->validated();
        $comm = Commentary::whereId($comm_id)->first();
        $post = Post::whereId($comm['post_id'])->first();
        $dashboard = Dashboard::whereId($post['dashboard_id'])->first();
        $owner = $dashboard['owner_id'];
        if (auth()->user()->id == $owner
            || auth()->user()->id == $post['author_id']
            || auth()->user()->id == $comm['author_id'])
        {
            Commentary::whereId($comm_id)->delete();
            return $this->noContent();
        }
        else
            return $this->forbidden("Permission denied: You're not allowed to delete this post.");
    }
}
