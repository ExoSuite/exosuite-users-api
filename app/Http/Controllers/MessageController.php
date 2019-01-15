<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Notification;
use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
use App\Events\DeletedMessageEvent;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\DestroyMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Models\Message;
use App\Models\Group;
use App\Notifications\Message\NewMessageNotification;
use Illuminate\Support\Facades\Auth;

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
        //$this->authorize("createGroupMessage", $group);
        $data = $request->validated();
        $current_user = Auth::user();
        $data['user_id'] = auth()->user()->id;
        /** @var Message $message */
        $message = $group->messages()->create($data);
        broadcast(new NewMessageEvent($group, $message));
        $users = $group->users()->get();
        $users = $users->filter(function ($user) use ($current_user) {
            return $user->id != $current_user->id;
        });
        Notification::send($users, new NewMessageNotification($message));
        return $this->created($message);
    }

    /**
     * @param UpdateMessageRequest $request
     * @param Group $group
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMessageRequest $request, Group $group, Message $message)
    {
        $data = $request->validated();
        $message->update($data);
        broadcast(new ModifyMessageEvent($group, $message));
        return $this->ok($message);
    }

    /**
     * @param Group $group
     * @return mixed
     */
    public function index(Group $group)
    {
        $messages = $group->messages()->get();
        return $this->ok($messages);
    }

    /**
     * @param Group $group
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Group $group, Message $message)
    {
        $message->delete();
        broadcast(new DeletedMessageEvent($group, $message));
        return $this->noContent();
    }
}
