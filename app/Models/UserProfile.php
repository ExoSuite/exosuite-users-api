<?php declare(strict_types = 1);

namespace App\Models;

use App\Enums\CollectionPicture;
use App\Enums\MediaConversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 */
class UserProfile extends Model implements HasMedia
{
    use HasMediaTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /** @var string[] */
    protected $fillable = [
        'id', 'birthday', 'city', 'description', 'avatar_id', 'cover_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
