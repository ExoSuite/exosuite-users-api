<?php

namespace App\Http\Controllers;

use App\Events\NewMessageEvent;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Http\Request;

/**
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    /**
     * @param CreateMessageRequest $request
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateMessageRequest $request, Group $group)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        /** @var Message $message */
        $message = $group->messages()->create($data);
        //broadcast(new NewMessageEvent($group, $message));
        return $this->created($message);
    }

    /**
     * @param UpdateMessageRequest $request
     * @param Group $group
     * @param Message $message
     */
    public function update(UpdateMessageRequest $request, Group $group, Message $message)
    {
        // TODO: VERIFIER SI LE GUSSE EST BIEN DANS LE GROUPE DU MESSAGE ? GET GROUP ID DU MESSAGE ET VERIFIER QUE LE CURRENT USER EST DANS LE GROUPE ?

        $data = $request->validated();
        $message->update(['message_contents' => $data['message_contents']]);
    }

    /**
     * @param CreateMessageRequest $request
     * @return mixed
     */
    public function index(CreateMessageRequest $request)
    {
        $data = $request->validated();
        $messages = Message::whereAuthorId(auth()->user()->id)->get();
        return $messages;
    }

    /**
     * @param CreateMessageRequest $request
     */
    public function destroy(CreateMessageRequest $request)
    {
        $data = $request->validated();
        $entity = Follow::whereUserId(auth()->user()->id)->whereFollowedId($data['id']);
        if ($entity->exists())
            $entity->delete();
    }
}
