<?php

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
 * Class DeletedMessageEvent
 * @package App\Events
 */
class DeletedMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var string
     */
    public $broadcastQueue = Queue::MESSAGE;
    /**
     * @Group Group
     */
    public $group;
    /**
     * @Message Message
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param Group $group_
     * @param Message $message_
     */
    public function __construct($group_, $message_)
    {
        $this->group = $group_;
        $this->message = $message_;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel("group.{$this->group->id}");
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return MessageBroadcastType::DELETED_MESSAGE;
    }

    /**
     *
     */
    public function broadcastWith()
    {
        return ["id" => $this->message->id];
    }
}
