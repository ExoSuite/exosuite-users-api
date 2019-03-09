<?php declare(strict_types = 1);

namespace App\Notifications\Message;

use App\Enums\NotificationType;
use App\Models\Message;
use App\Notifications\ExoSuiteNotification;
use Illuminate\Bus\Queueable;

/**
 * Class NewMessageNotification
 *
 * @package App\Notifications\Message
 */
class NewMessageNotification extends ExoSuiteNotification
{
    use Queueable;

    /** @var \App\Models\Message */
    public $message;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return  array<string, \App\Models\Message|string>
     */
    public function toArray($notifiable): array
    {
        return [
            'data' => $this->message,
            'notification_type' => NotificationType::NEW_MESSAGE,
        ];
    }
}
