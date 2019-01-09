<?php

namespace App\Events;

use App\Enums\MessageBroadcastType;
use App\Enums\Queue;
use App\Models\Message;
use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class NewMessageEvent
 * @package App\Events
 */
class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
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
        return $this->message->id;
    }
}