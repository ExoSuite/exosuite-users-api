<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 07/11/2018
 * Time: 18:28
 */

namespace App\Pivots;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Webpatser\Uuid\Uuid;
use function array_key_exists;

/**
 * Class UuidPivot
 * @package App\Pivots
 */
abstract class UuidPivot extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = false;

    /**
     * Create a new pivot model from raw values returned from a query.
     *
     * @param  \Illuminate\Database\Eloquent\Model $parent
     * @param  array $attributes
     * @param  string $table
     * @param  bool $exists
     *
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     * @throws \Exception
     */
    public static function fromRawAttributes(Model $parent, array $attributes, string $table, bool $exists = false): Pivot
    {
        if (!$exists and !array_key_exists('id', $attributes)) {
            $attributes['id'] = Uuid::generate()->string;
        }

        return parent::fromRawAttributes($parent, $attributes, $table, $exists);
    }
}
