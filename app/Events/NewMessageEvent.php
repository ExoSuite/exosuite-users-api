<?php

namespace App\Events;

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
     * @var Group
     */
    private $group;
    /**
     * @var Message
     */
    private $message;

    /**
     * Create a new event instance.
     *
     * @param Group $group
     * @param Message $message
     */
    public function __construct($group, $message)
    {
        $this->group = $group;
        $this->message = $message;
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
        return "NewMessage";
    }

    /**
     *
     */
    public function broadcastWith()
    {
        return $this->message;
    }
}
