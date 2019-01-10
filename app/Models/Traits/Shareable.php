<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 17/11/2018
 * Time: 17:32
 */

namespace App\Models\Traits;

use App\Models\Share;

/**
 * Trait Shareable
 * @package App\Models\Traits
 */
trait Shareable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function share()
    {
        return $this->morphMany(
            Share::class,
            Share::SHARE_RELATION_NAME
        );
    }
}
