<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class MessageBroadcastType extends Enum
{
    const CREATED_MESSAGE = "NewMessage";
    const MODIFIED_MESSAGE = "ModifiedMessage";
    const DELETED_MESSAGE = "DeletedMessage";
}
