<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class NotificationType
 *
 * @package App\Enums
 */
final class NotificationType extends Enum
{

    const FOLLOW = "follow";
    const NEW_MESSAGE = "new_message";
    const NEW_GROUP = "new_group";
    const DELETED_GROUP = "deleted_group";
    const EXPELLED_FROM_GROUP = "expelled_from_group";
}
