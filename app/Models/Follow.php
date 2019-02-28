<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

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

    /** @var string[] */
    protected $fillable = [
        'follow_id',
        'user_id',
        'followed_id',
    ];
}
