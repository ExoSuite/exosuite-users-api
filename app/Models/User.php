<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

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
    use Notifiable;

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
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'email_verified_at', 'remember_token'];

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

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return "users.{$this->id}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    /**
     * Checks if User has access to $permissions.
     * @param array $permissions
     * @return bool
     */
    public function hasAccess(array $permissions): bool
    {
        // check if the permission is available in any role
        foreach ($this->roles() as $role) {
            if ($role->hasAccess($permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the user belongs to role.
     * @param string $roleSlug
     * @return bool
     */
    public function inRole(string $roleSlug)
    {
        return $this->roles()->where('slug', strtolower($roleSlug))->exists();
    }

    /**
     * @param string $role
     * @return void
     */
    public function addRole(string $role)
    {
        /** @var Role $roleModel */
        $roleModel = $this->roles()->getRelated();
        $this->roles()->attach($roleModel->getIdFromRoleName($role));
    }
}
