<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\Commentary\CreateCommentaryRequest;
use App\Http\Requests\Commentary\UpdateCommentaryRequest;
use App\Models\Commentary;
use App\Models\Dashboard;
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
        return $this->created($this->createComm($request->validated(), $post));
    }

    public function getCommsFromPost(User $user, Dashboard $dashboard, Post $post): JsonResponse
    {
        return $this->ok(Commentary::wherePostId($post->id)->get());
    }

    public function updateComm(
        UpdateCommentaryRequest $request,
        User $user,
        Dashboard $dashboard,
        Post $post,
        Commentary $commentary
    ): JsonResponse
    {
        return $this->ok($this->updateCommentary($request->validated(), $commentary));
    }

    public function deleteComm(User $user, Dashboard $dashboard, Post $post, Commentary $commentary): JsonResponse
    {
        Commentary::whereId($commentary->id)->delete();

        return $this->noContent();
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
}
