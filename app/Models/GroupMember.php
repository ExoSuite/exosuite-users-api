<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GroupMember
 *
 * @property bool $is_admin
 * @package App\Models
 * @property string $id
 * @property string $group_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GroupMember whereUserId($value)
 * @mixin \Eloquent
 */
class GroupMember extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'group_id',
        'user_id',
        'is_admin',
        'created_at',
        'updated_at',
    ];

    /** @var string[] */
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
        return (bool) $this->is_admin;
    }
}
