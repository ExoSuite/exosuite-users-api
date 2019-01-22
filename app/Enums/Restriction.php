<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-12-05
 * Time: 17:57
 */

namespace App\Enums;


use BenSampo\Enum\Enum;

final class Restriction extends Enum
{
    const PRIVATE = "private";
    const FRIENDS = "friends";
    const FRIENDS_FOLLOWERS = "followers";
    const PUBLIC = "public";
}