<?php declare(strict_types = 1);

namespace App\Events;

use App\Enums\MessageBroadcastType;
use App\Enums\Queue;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewMessageEvent
 *
 * @package App\Events
 */
class NewMessageEvent implements ShouldBroadcast
{

    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /** @var string */
    public $broadcastQueue = Queue::MESSAGE;
    /** @var \App\Models\Group */
    public $group;
    /** @var \App\Models\Message */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Group $group_
     * @param \App\Models\Message $message_
     */
    public function __construct(Group $group_, Message $message_)
    {
        $this->group = $group_;
        $this->message = $message_;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PresenceChannel;
     */
    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel("group.{$this->group->id}");
    }

    public function broadcastAs(): string
    {
        return MessageBroadcastType::CREATED_MESSAGE;
    }

    /**
     * @return mixed
     */
    public function broadcastWith()
    {
        return $this->message->toArray();
    }
}
