<?php declare(strict_types = 1);

namespace App\Models;

use App\Pivots\RoleUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use function strtolower;

/**
 * Class Role
 *
 * @package App\Models
 * @property string $name
 * @property string $slug
 * @property array $permissions
 */
class Role extends Model
{
    /** @var string[] */
    protected $fillable = [
        'name', 'slug', 'permissions',
    ];
    /** @var string[] */
    protected $casts = [
        'permissions' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(
            static function (Role $model): void {
                $model->slug = strtolower($model->name);
            }
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(RoleUser::class);
    }

    /**
     * @param string[] $permissions
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

    private function hasPermission(string $permission): bool
    {
        return $this->permissions[$permission] ?? false;
    }

    /**
     * @param string $roleName
     * @return \App\Models\Role|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public function getIdFromRoleName(string $roleName)
    {
        return self::whereName($roleName)->first([$this->primaryKey]);
    }
}
