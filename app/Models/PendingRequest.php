<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

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

    /** @var string[] */
    protected $fillable = [
        'request_id',
        'requester_id',
        'type',
        'target_id',
    ];
}