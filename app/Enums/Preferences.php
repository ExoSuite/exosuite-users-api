<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: dupil_l
 * Date: 19-04-03
 * Time: 15:25
 */

namespace App\Enums;


use BenSampo\Enum\Enum;

final class Preferences extends Enum
{
    const FULL_NAME = "full_name";
    const NICKNAME = "nickname";
}