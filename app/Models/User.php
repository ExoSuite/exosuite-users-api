<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

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
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'password' ];

    protected static function boot()
    {
        self::UuidBoot();
        static::creating(
            function (User $model) {
                $model->password = Hash::make( $model->password );
            }
        );
    }

    public function profile()
    {
        $this->hasOne( UserProfile::class );
    }
}
