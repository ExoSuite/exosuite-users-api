<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 07/11/2018
 * Time: 18:32
 */

namespace App\Models\Abstracts;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UuidModel
 * @package App\Models\Abstracts
 */
abstract class UuidModel extends Model
{
    use Uuids;
    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = false;

    public static function getTableName(): string
    {
        return (new static)->getTable();
    }
}
