<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TokenScope extends Enum
{
    const VIEW_PICTURE = 'view-picture';
    const CONNECT_IO = 'connect-io';
    const MESSAGE = "message";
    const GROUP = "group";
}
