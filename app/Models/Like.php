<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Like
 *
 * @package App\Models
 */
class Like extends UuidModel
{
    use Uuids;

    /** @var string[] */
    protected $fillable = [
        'id',
        'liked_id',
        'liked_type',
        'liker_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
