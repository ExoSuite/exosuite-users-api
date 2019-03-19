<?php declare(strict_types = 1);

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
 *
 * @package App\Models\Traits
 */
trait Uuids
{

    /**
     * Boot function from laravel.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            static function (Model $model): void {
                $model->{$model->getKeyName()} = Uuid::generate()->string;
            }
        );
    }
}
