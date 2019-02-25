<?php declare(strict_types = 1);

namespace App\Notifications;

use App\Enums\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ExoSuiteNotification
 *
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
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $message = new BroadcastMessage($this->toArray($notifiable));

        return $message->onQueue(Queue::NOTIFICATION);
    }

    /**
     * @param mixed $notifiable
     * @return array
     */
    abstract public function toArray($notifiable): array;
}
