<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Message
 *
 * @package App\Models
 */
class Message extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id', 'contents', 'group_id', 'user_id', 'created_at', 'updated_at',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
