<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\CollectionPicture;
use App\Enums\MediaConversion;
use App\Enums\Restriction;
use App\Enums\Roles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class UserProfile
 *
 * @package App\Models
 * @property \Webpatser\Uuid\Uuid $id
 * @property \Carbon\Carbon $birthday
 * @property string $city
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $avatar_id
 * @property string|null $cover_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereAvatarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCoverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserProfile whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read int|null $media_count
 */
class UserProfile extends Model implements HasMedia
{
    use HasMediaTrait;

    /** @var array<mixed> */
    protected $relationsValidation;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /** @var string[] */
    protected $fillable = [
        'id',
        'birthday',
        'city',
        'description',
        'avatar_id',
        'cover_id',
    ];

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

    public function initPointersArray(): void
    {
        $this->relationsValidation = [
            Restriction::PUBLIC => [$this, 'allowPublic'],
            Restriction::FRIENDS_FOLLOWERS => [$this, 'checkFollow'],
            Restriction::FRIENDS => [$this, 'checkFriendship'],
            Restriction::PRIVATE => [$this, 'denyPrivate'],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    /**
     * @param \Spatie\MediaLibrary\Models\Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion(MediaConversion::THUMB)
            ->width(124)
            ->height(124)
            ->performOnCollections(CollectionPicture::AVATAR);
        $this->addMediaConversion(MediaConversion::BANNER)
            ->width(1920)
            ->height(640)
            ->performOnCollections(CollectionPicture::COVER);
    }

    public function getDescriptionAttribute(?string $description): ?string
    {
        $user = Auth::user();
        $target = $this->user()->first();

        if (!$description) {
            return null;
        }

        if ($user->inRole(Roles::ADMINISTRATOR)
            || $user->id === $target->id) {
            return $description;
        }

        $owner_and_profileRestrictions = $target->with('profileRestrictions')->where(
            'id',
            $target->id
        )->first();

        return call_user_func(
            $this->relationsValidation[$owner_and_profileRestrictions->profileRestrictions()->first()->description],
            $user->id,
            'description'
        );
    }

    public function getCityAttribute(?string $city): ?string
    {
        $user = Auth::user();
        $target = $this->user()->first();

        if (!$city) {
            return null;
        }

        if ($user->inRole(Roles::ADMINISTRATOR)
            || $user->id === $target->id) {
            return $city;
        }

        $owner_and_profileRestrictions = $target->with('profileRestrictions')->where(
            'id',
            $target->id
        )->first();

        return call_user_func(
            $this->relationsValidation[$owner_and_profileRestrictions->profileRestrictions()->first()->city],
            $user->id,
            'city'
        );
    }

    public function getBirthdayAttribute(?string $birthday): ?string
    {
        $user = Auth::user();
        $target = $this->user()->first();

        if (!$birthday) {
            return null;
        }

        if ($user->inRole(Roles::ADMINISTRATOR)
            || $user->id === $target->id) {
            return $birthday;
        }

        $owner_and_profileRestrictions = $target->with('profileRestrictions')->where(
            'id',
            $target->id
        )->first();

        return call_user_func(
            $this->relationsValidation[$owner_and_profileRestrictions->profileRestrictions()->first()->birthday],
            $user->id,
            'birthday'
        );
    }

    public function allowPublic(string $claimer_id, string $field): string
    {
        return $this->attributes[$field];
    }

    public function checkFriendship(string $claimer_id, string $field): ?string
    {
        if (Friendship::whereUserId($this->attributes['id'])->whereFriendId($claimer_id)->exists()
            && Friendship::whereFriendId($this->attributes['id'])->whereUserId($claimer_id)->exists()) {
            return $this->attributes[$field];
        }

        return null;
    }

    public function checkFollow(string $claimer_id, string $field): ?string
    {
        if (Follow::whereFollowedId($this->attributes['id'])->whereUserId($claimer_id)->exists()) {
            return $this->attributes[$field];
        }

        return null;
    }

    public function denyPrivate(string $claimer_id, string $field): ?string
    {
        return null;
    }

}
