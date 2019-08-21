<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Events\DeletedMessageEvent;
use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
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
 *
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    public const GET_PER_PAGE = 30;

    public function __construct()
    {
        $this->middleware("scope:message");
    }

    public function store(CreateMessageRequest $request, Group $group): JsonResponse
    {
        $data = $request->validated();
        $current_user_id = Auth::id();
        $data['user_id'] = $current_user_id;
        /** @var \App\Models\Message $message */
        $message = $group->messages()->create($data)->load("user");
        broadcast(new NewMessageEvent($group, $message));
        $users = static::collectionFilterWithExcept(
            $group->users()->get(),
            'id',
            $current_user_id
        );

        Notification::send($users, new NewMessageNotification($message));

        return $this->created($message);
    }

    public function update(UpdateMessageRequest $request, Group $group, Message $message): JsonResponse
    {
        $data = $request->validated();
        $message = $message->load("user");
        $message->update($data);
        broadcast(new ModifyMessageEvent($group, $message));

        return $this->ok($message);
    }

    /**
     * @param \App\Models\Group $group
     * @return mixed
     */
    public function index(Group $group)
    {
        return $this->ok($group->messages()->latest()->with(['user'])->paginate(self::GET_PER_PAGE));
    }

    public function destroy(Group $group, Message $message): JsonResponse
    {
        $message = $message->load("user");
        $message->delete();
        broadcast(new DeletedMessageEvent($group, $message));

        return $this->ok($message);
    }
}
