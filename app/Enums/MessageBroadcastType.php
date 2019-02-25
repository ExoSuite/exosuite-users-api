<?php declare(strict_types = 1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class MessageBroadcastType
 *
 * @package App\Enums
 */
final class MessageBroadcastType extends Enum
{

    const CREATED_MESSAGE = "NewMessage";
    const MODIFIED_MESSAGE = "ModifiedMessage";
    const DELETED_MESSAGE = "DeletedMessage";
}
