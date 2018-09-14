<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 11/09/2018
 * Time: 21:32
 */

namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

/**
 * Trait Uuids
 * @package App\Models\Traits
 */
trait Uuids
{


    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(
            function (Model $model) {
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        );

    }//end boot()


}
