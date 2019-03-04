<?php declare(strict_types = 1);

namespace App\Notifications;

use App\Enums\NotificationType;

/**
 * Class FollowNotification
 *
 * @package App\Notifications
 */
class FollowNotification extends ExoSuiteNotification
{

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return string[]
     */
    public function toArray($notifiable): array
    {
        return [
            'data' => 'new follow!',
            'notification_type' => NotificationType::FOLLOW,
        ];
    }
}
