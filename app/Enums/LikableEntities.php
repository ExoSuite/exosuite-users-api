<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loic
 * Date: 19-01-11
 * Time: 15:48
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Class LikableEntities
 *
 * @package App\Enums
 */
final class LikableEntities extends Enum
{

    const POST = "post";
    const COMMENTARY = "commentary";
    const RUN = "run";
}
