<?php

namespace App\Models;

use App\Enums\Visibility;
use App\Models\Abstracts\UuidModel;
use App\Models\Traits\Shareable;
use App\Pivots\UserShare;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;

/**
 * Class Run
 * @package App\Models
 * @property Uuid $id
 * @property string $description
 * @property Uuid $creator_id
 * @property Visibility $visibility
 * @property string $name
 */
class Run extends UuidModel
{
    use Shareable;

    /**
     * define relation key
     */
    const USER_FOREIGN_KEY = 'creator_id';

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'description', 'creator_id', 'visibility', 'name'
    ];

    /**
     * if no visibility is provided set to private
     */
    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $run) {
            if (!$run->visibility) {
                $run->visibility = Visibility::PRIVATE;
            }
            $run->creator_id = Auth::id();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkpoints()
    {
        return $this->hasMany(CheckPoint::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, self::USER_FOREIGN_KEY);
    }
}
