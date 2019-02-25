<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Model;
use App\Models\Traits\Uuids;

/**
 * Class PendingRequest
 *
 * @package App\Models
 */
class PendingRequest extends Model
{

    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'request_id';

    /** @var array */
    protected $fillable = [
        'request_id', 'requester_id', 'type', 'target_id',
    ];
}
