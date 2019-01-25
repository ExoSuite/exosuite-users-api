<?php

namespace App\Notifications;

use App\Enums\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ExoSuiteNotification
 * @package App\Notifications
 */
abstract class ExoSuiteNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    abstract public function toArray($notifiable);

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $message = new BroadcastMessage($this->toArray($notifiable));
        return $message->onQueue(Queue::NOTIFICATION);
    }
}
