<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

/**
 * Class User
 * @package App\Models
 */

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use Searchable;
    use Uuids {
        boot as UuidBoot;
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'nick_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'email_verified_at'];

    /**
     * @return void
     */
    protected static function boot()
    {
        self::UuidBoot();
        static::creating(
            function (User $model) {
                $model->password = Hash::make($model->password);
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'id');
    }
}
