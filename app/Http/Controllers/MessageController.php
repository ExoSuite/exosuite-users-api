<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\CreateMessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function store(CreateMessageRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->user()->id;
        $message = Message::create($data);
        return $this->created($message);
    }

    public function update(CreateMessageRequest $request, Group $group, Message $message)
    {
        // TODO : NOUVELLE REQUEST OU ON VERIFIE QUE LE MESSAGE EXISTE BIEN (SI l'USER EST AUTORISE A RM LE MESSAGE) / LARAVEL VALIDATOR DOC
        $data = $request->validated();
        $message->update(['message_contents' => $data['message_contents']]);
    }

    public function index(CreateMessageRequest $request)
    {
        $data = $request->validated();
        $messages = Message::whereAuthorId(auth()->user()->id)->get();
        return $messages;
    }

    public function destroy(CreateMessageRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id']);
        if ($entity->exists())
            $entity->delete();
    }
}
