<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PendingRequest
 *
 * @package App\Models
 * @property string $id
 * @property string $requester_id
 * @property string $type
 * @property string $target_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $target
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingRequest whereUpdatedAt($value)
 * @mixin \Eloquent
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
