<?php declare(strict_types = 1);

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Notifications\ExoSuiteNotification;

/**
 * Class NewGroupNotification
 *
 * @package App\Notifications
 */
class NewGroupNotification extends ExoSuiteNotification
{

    /**
     * @var array|string
     */
    private $message;
    /**
     * @var array
     */
    private $group;

    /**
     * Create a new notification instance.
     *
     * @param string|array $message
     * @param array $group
     */
    public function __construct($message, array $group)
    {
        $this->message = $message;
        $this->group = $group;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'data' => [
                "message" => $this->message,
                "group" => $this->group,
            ],
            'notification_type' => NotificationType::NEW_GROUP,
        ];
    }
}
