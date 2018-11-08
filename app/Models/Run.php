<?php

namespace App\Models;

use App\Enums\RunVisibility;
use App\Models\Abstracts\UuidModel;

/**
 * Class Run
 * @package App\Models
 */
class Run extends UuidModel
{

    /**
     * @var array
     */
    protected $fillable = ['id', 'description', 'creator_id', 'visibility', 'name'];

    /**
     * if no visibility is provided set to private
     */
    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $run) {
            if (!$run->visibility) {
                $run->visibility = RunVisibility::PRIVATE;
            }
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
