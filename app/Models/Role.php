<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App\Models
 */
class Role extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'permissions',
    ];
    /**
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users');
    }

    /**
     * @param array $permissions
     * @return bool
     */
    public function hasAccess(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $permission
     * @return bool
     */
    private function hasPermission(string $permission): bool
    {
        return $this->permissions[$permission] ?? false;
    }


    /**
     *
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(
            function (Role $model) {
                $model->slug = strtolower($model->name);
            }
        );
    }

    /**
     * @param string $roleName
     * @return Role|Model|null|object
     */
    public function getIdFromRoleName(string $roleName)
    {
        return self::where('name', $roleName)->first(['id']);
    }
}
