<?php

namespace App\Notifications\Message;

use App\Enums\NotificationType;
use App\Models\Message;
use App\Notifications\ExoSuiteNotification;
use Illuminate\Bus\Queueable;

/**
 * Class NewMessageNotification
 * @package App\Notifications\Message
 */
class NewMessageNotification extends ExoSuiteNotification
{
    use Queueable;

    /**
     * @var Message
     */
    public $message;

    /**
     * Create a new notification instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => $this->message,
            'notification_type' => NotificationType::NEW_MESSAGE
        ];
    }
}
