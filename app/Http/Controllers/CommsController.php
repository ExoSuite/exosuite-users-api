<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentaryRequest;
use App\Http\Requests\DeleteCommentaryRequest;
use App\Http\Requests\GetCommentariesRequest;
use App\Http\Requests\UpdateCommentaryRequest;
use App\Models\Commentary;
use Illuminate\Http\Request;
use PhpParser\Comment;
use App\Models\Post;
use App\Models\Dashboard;

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
        return Commentary::wherePostId($data['id'])->get();
    }

    private function deleteCommentary(array $data)
    {
        $comm = Commentary::whereId($data['id'])->first();
        $comm->delete();
    }

    public function store(CreateCommentaryRequest $request)
    {
        $comm = $this->createComm($request->validated());
        return $this->created($comm);
    }

    public function getCommsFromPost(GetCommentariesRequest $request)
    {
        $comms = $this->getComms($request->validated());
        return $this->ok($comms);
    }

    public function updateComm(UpdateCommentaryRequest $request)
    {
        $comm = $this->updateCommentary($request->validated());
        return $this->ok($comm);
    }

    public function deleteComm(DeleteCommentaryRequest $request)
    {
        $this->deleteCommentary($request->validated());
        return $this->noContent();
    }
}
