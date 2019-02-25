<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GroupMember
 *
 * @property bool $is_admin
 * @package App\Models
 */
class GroupMember extends UuidModel
{

    /** @var array */
    protected $fillable = [
        'id', 'group_id', 'user_id', 'is_admin', 'created_at', 'updated_at',
    ];

    /** @var array */
    protected $hidden = [
        'id',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAdmin(): bool
    {
        return (bool)$this->is_admin;
    }
}
