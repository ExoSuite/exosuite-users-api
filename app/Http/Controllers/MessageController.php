<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Events\DeletedMessageEvent;
use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Models\Group;
use App\Models\Message;
use App\Notifications\Message\NewMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use function broadcast;

/**
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    public function store(CreateMessageRequest $request, Group $group): JsonResponse
    {
        //$this->authorize("createGroupMessage", $group);
        $data = $request->validated();
        $current_user = Auth::user();
        $data['user_id'] = $current_user->id;
        /** @var \App\Models\Message $message */
        $message = $group->messages()->create($data);
        broadcast(new NewMessageEvent($group, $message));
        $users = $group->users()->get();
        $users = $users->filter(static function ($user) use ($current_user) {
            return $user->id != $current_user->id;
        });
        Notification::send($users, new NewMessageNotification($message));

        return $this->created($message);
    }

    public function update(UpdateMessageRequest $request, Group $group, Message $message): JsonResponse
    {
        $data = $request->validated();
        $message->update($data);
        broadcast(new ModifyMessageEvent($group, $message));

        return $this->ok($message);
    }

    /**
     * @param \App\Models\Group $group
     *
     * @return mixed
     */
    public function index(Group $group)
    {
        $messages = $group->messages()->get();

        return $this->ok($messages);
    }

    public function destroy(Group $group, Message $message): JsonResponse
    {
        $message->delete();
        broadcast(new DeletedMessageEvent($group, $message));

        return $this->noContent();
    }
}
