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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use ScoutElastic\Searchable;

/**
 * Class Run
 *
 * @package App\Models
 * @property \App\Models\Uuid $id
 * @property string $description
 * @property string $creator_id
 * @property \App\Enums\Visibility $visibility
 * @property string $name
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

        return array_merge($data, $this->user()->first()->only(["nick_name", "first_name", "last_name"]));
    }

}
