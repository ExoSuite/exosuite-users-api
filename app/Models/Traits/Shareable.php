<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 17/11/2018
 * Time: 17:32
 */

namespace App\Models\Traits;

use App\Models\Share;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Shareable
 *
 * @package App\Models\Traits
 */
trait Shareable
{

    public function share(): MorphMany
    {
        return $this->morphMany(
            Share::class,
            Share::SHARE_RELATION_NAME
        );
    }
}
