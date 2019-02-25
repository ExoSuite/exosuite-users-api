<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Model;
use App\Models\Traits\Uuids;

/**
 * Class Follow
 *
 * @package App\Models
 */
class Follow extends Model
{

    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'follow_id';

    /** @var array */
    protected $fillable = [
        'follow_id', 'user_id', 'followed_id',
    ];
}
