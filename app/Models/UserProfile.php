<?php

namespace App\Models;

use App\Enums\CollectionPicture;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class UserProfile
 * @package App\Models
 * @property Uuid $id
 * @property Carbon $birthday
 * @property string $city
 * @property string $description
 */
class UserProfile extends Model implements HasMedia
{
    use HasMediaTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id', 'birthday', 'city', 'description', 'avatar_id', 'cover_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->width(124)
            ->height(124)
            ->performOnCollections(CollectionPicture::AVATAR);
        $this->addMediaConversion('banner')
            ->width(1920)
            ->height(640)
            ->performOnCollections(CollectionPicture::COVER);
    }
}
