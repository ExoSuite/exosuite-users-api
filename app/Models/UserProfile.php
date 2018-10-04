<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfile
 * @package App\Models
 */
class UserProfile extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'birthday', 'city', 'description'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
