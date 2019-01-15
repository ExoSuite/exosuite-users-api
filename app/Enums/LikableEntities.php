<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 19-01-11
 * Time: 15:48
 */

namespace App\Enums;


use BenSampo\Enum\Enum;

final class LikableEntities extends Enum
{
    const POST = "post";
    const COMMENTARY = "commentary";
    const RUN = "run";
 }