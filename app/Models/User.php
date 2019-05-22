<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\Preferences;
use App\Enums\Restriction;
use App\Enums\Roles;
use App\Models\Indexes\UserIndexConfigurator;
use App\Models\SearchRules\UserSearchRule;
use App\Pivots\RoleUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use ScoutElastic\Searchable;
use Webpatser\Uuid\Uuid;
use function strtolower;

/**
 * Class User
 *
 * @package App\Models
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $nick_name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $email_verified_at
 */
class User extends \Illuminate\Foundation\Auth\User
{
    use HasApiTokens;
    use Searchable;
    use Notifiable;

    /** @var array<mixed> */
    protected $relationsValidation;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /** @var string */
    protected $indexConfigurator = UserIndexConfigurator::class;

    /** @var string[] */
    protected $searchRules = [
        UserSearchRule::class,
    ];

    /** @var array<string, array<string, array<string, string>>> */
    protected $mapping = [
        'properties' => [
            'first_name' => [
                'type' => 'completion',
            ],
            'last_name' => [
                'type' => 'completion',
            ],
            'nick_name' => [
                'type' => 'completion',
            ],
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'nick_name',
        'email',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(
            static function (User $user): void {
                $user->password = Hash::make($user->password);
                $user->{$user->getKeyName()} = Uuid::generate()->string;
            }
        );

        static::created(static function (User $user): void {
            $user->profile()->create();
            $user->dashboard()->create();
            $user->profileRestrictions()->create();
        });
    }

    /**
     * User constructor.
     *
     * @param array<mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->initPointersArray();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, $this->primaryKey);
    }

    public function initPointersArray(): void
    {
        $this->relationsValidation = [
            Restriction::PUBLIC => [$this, 'allowPublic'],
            Restriction::FRIENDS_FOLLOWERS => [$this, 'checkFollow'],
            Restriction::FRIENDS => [$this, 'checkFriendship'],
            Restriction::PRIVATE => [$this, 'denyPrivate'],
        ];
    }

    public function getPublicProfile(?User $user = null): User
    {
        if (!$user) {
            $user = Auth::user();
        }

        $restrictions = $this->profileRestrictions()->first();
        $profile = $this->load('profile');
        $profile['follow'] = ['status' => false];
        $follow = Follow::whereUserId($user->id)->whereFollowedId($this->id);

        if ($follow->exists()) {
            $profile['follow'] = [
                'status' => true,
                'follow_id' => $follow->first()->id,
            ];
        }

        if ($user->inRole(Roles::ADMINISTRATOR)) {
            return $profile;
        }

        if ($restrictions['nomination_preference'] === Preferences::NICKNAME && $profile['nick_name'] !== null) {
            $profile['first_name'] = null;
            $profile['last_name'] = null;
        } else {
            $profile['nick_name'] = null;

        }

        $fields = ['city', 'description', 'birthday'];

        foreach ($fields as $field) {
            $profile = call_user_func(
                $this->relationsValidation[$restrictions[$field]],
                $user->id,
                $profile,
                $field
            );
        }

        return $profile;
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
     * Checks if User has access to $permissions.
     *
     * @param string[] $permissions
     * @return bool
     */
    public function hasAccess(array $permissions): bool
    {
        // check if the permission is available in any role
        $roles = $this->roles()->getModels();

        /** @var \App\Models\Role $role */
        foreach ($roles as $role) {
            if ($role->hasAccess($permissions)) {
                return true;
            }
        }

        return false;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->using(RoleUser::class);
    }

    /**
     * Checks if the user belongs to role.
     *
     * @param string $roleSlug
     * @return bool
     */
    public function inRole(string $roleSlug): bool
    {
        return $this->roles()->whereSlug(strtolower($roleSlug))->exists();
    }

    public function addRole(string $role): void
    {
        /** @var \App\Models\Role $roleModel */
        $roleModel = $this->roles()->getRelated();
        $this->roles()->attach($roleModel->getIdFromRoleName($role));
    }

    public function runs(): HasMany
    {
        return $this->hasMany(Run::class, Run::USER_FOREIGN_KEY)
            ->with(['checkpoints']);
    }

    public function sharedRuns(): MorphToMany
    {
        return $this->morphedByMany(
            Run::class,
            Share::SHARE_RELATION_NAME,
            Share::getTableName()
        )->withTimestamps();
    }

    public function friendships(string $related_to): HasMany
    {
        return $this->hasMany(Friendship::class, $related_to);
    }

    public function dashboard(): HasOne
    {
        return $this->hasOne(Dashboard::class, 'owner_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function commentaries(): HasMany
    {
        return $this->hasMany(Commentary::class, 'author_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'liker_id');
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'followed_id');
    }

    public function pendingRequests(string $related_to): HasMany
    {
        return $this->hasMany(PendingRequest::class, $related_to);
    }

    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(
            Group::class,
            GroupMember::class,
            'user_id',
            'id',
            'id',
            'group_id'
        )->with(['groupMembers', 'latestMessages']);
    }

    public function postsFromDashboard(): HasManyThrough
    {
        return $this->hasManyThrough(
            Post::class,
            Dashboard::class,
            'owner_id'
        );
    }

    /**
     * @return mixed[]
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nick_name' => $this->nick_name,
        ];
    }

    public function profileRestrictions(): HasOne
    {
        return $this->hasOne(ProfileRestrictions::class);
    }

    public function allowPublic(string $claimer_id, User $profile, string $field): User
    {
        return $profile;
    }

    public function checkFriendship(string $claimer_id, User $profile, string $field): User
    {
        if (Friendship::whereUserId($this->id)->whereFriendId($claimer_id)->exists()) {
            return $profile;
        }

        $profile['profile'][$field] = null;

        return $profile;
    }

    public function checkFollow(string $claimer_id, User $profile, string $field): User
    {
        if (Follow::whereFollowedId($this->id)->whereUserId($claimer_id)->exists()) {
            return $profile;
        }

        $profile['profile'][$field] = null;

        return $profile;
    }

    public function denyPrivate(string $claimer_id, User $profile, string $field): User
    {
        $profile['profile'][$field] = null;

        return $profile;
    }

}
