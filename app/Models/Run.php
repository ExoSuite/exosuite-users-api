<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\Visibility;
use App\Models\Abstracts\UuidModel;
use App\Models\Indexes\RunIndexConfigurator;
use App\Models\SearchRules\RunSearchRule;
use App\Models\Traits\Shareable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Auth;
use ScoutElastic\Searchable;

/**
 * Class Run
 *
 * @package App\Models
 * @property string $id
 * @property string $creator_id
 * @property string $name
 * @property string|null $description
 * @property string $visibility
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CheckPoint[] $checkpoints
 * @property \ScoutElastic\Highlight|null $highlight
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Share[] $share
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Time[] $timesThroughCheckpoints
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Run whereVisibility($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserRun[] $userRuns
 * @property-read int|null $checkpoints_count
 * @property-read int|null $likes_count
 * @property-read int|null $share_count
 * @property-read int|null $times_through_checkpoints_count
 * @property-read int|null $user_runs_count
 */
class Run extends UuidModel
{
    use Shareable;
    use Searchable;

    /**
     * define relation key
     */
    public const USER_FOREIGN_KEY = 'creator_id';

    /** @var string[] */
    private static $SearchableFields = ["id", "description", "name"];

    /** @var string[] */
    private static $UserSearchableFields = ["nick_name", "first_name", "last_name"];

    /** @var string */
    protected $indexConfigurator = RunIndexConfigurator::class;

    /** @var string[] */
    protected $searchRules = [
        RunSearchRule::class,
    ];

    /** @var array<string, array<string, array<string, string>>> */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'completion',
            ],
            'description' => [
                'type' => 'completion',
            ],
            "nick_name" => [
                'type' => 'completion',
            ],
        ],
    ];

    /** @var string[] */
    protected $fillable = [
        'id',
        'description',
        'creator_id',
        'visibility',
        'name',
        'updated_at',
        'created_at',
    ];

    /**
     * @return string[]
     */
    public static function getSearchableFields(): array
    {
        return self::$SearchableFields;
    }

    /**
     * @return string[]
     */
    public static function getUserSearchableFields(): array
    {
        return self::$UserSearchableFields;
    }

    /**
     * if no visibility is provided set to private
     */
    protected static function boot(): void
    {
        parent::boot();
        self::creating(static function (self $run): void {
            if (!$run->visibility) {
                $run->visibility = Visibility::PRIVATE;
            }

            $run->creator_id = Auth::id();
        });
        static::deleting(static function (self $run): void {
            $run->userRuns->each->delete();
            $run->checkpoints->each->delete();
        });
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(CheckPoint::class)->with(['times']);
    }

    public function timesThroughCheckpoints(): HasManyThrough
    {
        return $this->hasManyThrough(
            Time::class,
            CheckPoint::class,
            'run_id',
            'check_point_id',
            'id',
            'id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_FOREIGN_KEY);
    }

    public function likeFromUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            Like::class,
            User::class,
            'id',
            'liker_id',
            'creator_id',
            'id'
        );
    }

    public function likes(): HasMany
    {
        return $this->hasMany(
            Like::class,
            'liked_id'
        );
    }


    /**
     * @return mixed[]
     */
    public function toSearchableArray(): array
    {
        $data = $this->only(self::$SearchableFields);
        $data[self::USER_FOREIGN_KEY] = $this->{self::USER_FOREIGN_KEY};

        return array_merge($data, $this->user()->first()->only(self::$UserSearchableFields));
    }


    public function userRuns(): HasMany
    {
        return $this->hasMany(UserRun::class)->with(['times']);
    }
}
