<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class BindType
 *
 * @package App\Enums
 */
final class BindType extends Enum
{

    const GROUP = "group";
    const MESSAGE = "message";
    const NOTIFICATION = "notification";
    const USER = "user";
    const DASHBOARD = "dashboard";
    const POST = "post";
    const COMMENTARY = "commentary";
    const LIKE = "like";
    const FOLLOW = "follow";
    const FRIENDSHIP = "friendship";
    const PENDING_REQUEST = "request";
    const RUN = 'run';
    const CHECKPOINT = 'checkpoint';
    const TIME = 'time';
    const USER_RUN = "user_run";
    const RECORD = "record";
}
