<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Model;
use App\Models\Traits\Uuids;

/**
 * Class Friendship
 *
 * @package App\Models
 */
class Friendship extends Model
{

    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'friendship_id';

    /** @var array */
    protected $fillable = [
        'friendship_id', 'user_id', 'friend_id',
    ];
}
