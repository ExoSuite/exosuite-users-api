<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BindType extends Enum
{
    const GROUP = "group";
    const MESSAGE = "message";
    const NOTIFICATION = "notification";
    const USER = "user";
    const UUID = "uuid";
    const DASHBOARD = "dashboard";
    const POST = "post";
    const COMMENTARY = "commentary";
    const LIKE = "like";
    const FOLLOW = "follow";
    const FRIENDSHIP = "friendship";
    const PENDING_REQUEST = "request";
    const RUN = 'run';
}
