<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLiveChatMessageRequest;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;

class LiveChatMessageController extends Controller
{
    public function createMessage(array $data)
    {
        $data['author_id'] = auth()->user()->id;
        return LiveChatMessageController::create($data);
    }

    public function store(CreateLiveChatMessageRequest $request)
    {
        $message = $this->createMessage($request->validated());
        return $this->created($message);
    }

    public function editMessage(CreateLiveChatMessageRequest $request)
    {
        // TODO : NOUVELLE REQUEST OU ON VERIFIE QUE LE MESSAGE EXISTE BIEN (SI l'USER EST AUTORISE A RM LE MESSAGE) / LARAVEL VALIDATOR DOC
        $data = $request->validated();
        $message = LiveChatMessage::whereAuthorId(auth()->user()->id)->whereId($data['id'])->first();
        $message->update(['message_contents' => $data['message_contents']]);
    }

    public function getMyMessages(CreateLiveChatMessageRequest $request)
    {
        $data = $request->validated();
        $messages = LiveChatMessage::whereAuthorId(auth()->user()->id)->get();
        return $messages;
    }

    public function delete(CreateLiveChatMessageRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id']);
        if ($entity->exists())
            $entity->delete();
    }
}
