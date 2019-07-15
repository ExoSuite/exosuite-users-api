<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProfileRestrictions
 *
 * @property string $user_id
 * @property string $city
 * @property string $description
 * @property string $birthday
 * @property string $nomination_preference
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereNominationPreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProfileRestrictions whereUserId($value)
 * @mixin \Eloquent
 */
class ProfileRestrictions extends Model
{

    /** @var string */
    protected $primaryKey = "user_id";

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'city',
        'description',
        'birthday',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
