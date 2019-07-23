<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PendingRequest
 *
 * @package App\Models
 */
class PendingRequest extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'requester_id',
        'type',
        'target_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
