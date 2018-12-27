<?php

namespace App\Models;

use App\Models\Indexes\UserIndexConfigurator;
use App\Models\SearchRules\UserSearchRule;
use App\Pivots\RoleUser;
use App\Pivots\UserShare;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use ScoutElastic\Searchable;
use Webpatser\Uuid\Uuid;

/**
 * Class User
 * @package App\Models
 * @property Uuid $id
 * @property string $first_name
 * @property string $last_name
 * @property string $nick_name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $email_verified_at
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use Searchable;
    use Notifiable;

    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $indexConfigurator = UserIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        UserSearchRule::class
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'first_name' => [
                'type' => 'text',
                'analyzer' => 'standard'
            ],
            'last_name' => [
                'type' => 'text',
                'analyzer' => 'standard'
            ],
            'nick_name' => [
                'type' => 'text',
                'analyzer' => 'standard'
            ],
        ]
    ];

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
    protected $hidden = [
        'password', 'email_verified_at', 'remember_token'
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(
            function (self $user) {
                $user->password = Hash::make($user->password);
                $user->{$user->getKeyName()} = Uuid::generate()->string;
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, $this->primaryKey);
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return "users.{$this->id}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class);
    }

    /**
     * Checks if User has access to $permissions.
     * @param array $permissions
     * @return bool
     */
    public function hasAccess(array $permissions): bool
    {
        // check if the permission is available in any role
        $roles = $this->roles()->getModels();
        /** @var Role $role */
        foreach ($roles as $role) {
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
        return $this->roles()->whereSlug(strtolower($roleSlug))->exists();
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function runs(): HasMany
    {
        return $this->hasMany(Run::class, Run::USER_FOREIGN_KEY);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function sharedRuns(): MorphToMany
    {
        return $this->morphedByMany(
            Run::class,
            Share::SHARE_RELATION_NAME,
            Share::getTableName()
        )->withTimestamps();
    }

}
