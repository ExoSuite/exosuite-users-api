<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Like
 *
 * @package App\Models
 */
class Like extends Model
{
    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $primaryKey = 'like_id';

    /** @var string[] */
    protected $fillable = [
        'like_id',
        'liked_id',
        'liked_type',
        'liker_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
