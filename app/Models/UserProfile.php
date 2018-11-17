<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

/**
 * Class UserProfile
 * @package App\Models
 * @property Uuid id
 * @property \DateTime birthday
 * @property string city
 * @property string description
 */
class UserProfile extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var boolean
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
