<?php declare(strict_types = 1);

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Group;

/**
 * Class DeletedGroupNotification
 *
 * @package App\Notifications
 */
class DeletedGroupNotification extends ExoSuiteNotification
{

    /** @var string **/
    private $message;
    /** @var \App\Models\Group */
    private $group;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     * @param \App\Models\Group $group
     */
    public function __construct(string $message, Group $group)
    {
        $this->message = $message;
        $this->group = $group;
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
            'data' => [
                "message" => $this->message,
                "group" => $this->group,
            ],
            'notification_type' => NotificationType::DELETED_GROUP,
        ];
    }
}
