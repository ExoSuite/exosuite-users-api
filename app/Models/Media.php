<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Conversion\ConversionCollection;

/**
 * Class Media
 *
 * @package App\Models
 * @property string $id
 * @property string $model_type
 * @property string $model_id
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property int $size
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $responsive_images
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $extension
 * @property-read mixed $human_readable_size
 * @property-read mixed $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $model
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\MediaLibrary\Models\Media ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCollectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereManipulations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereResponsiveImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Media extends \Spatie\MediaLibrary\Models\Media
{
    use Uuids;

    /** @var bool */
    public $incrementing = false;

    /**
     * @param string $conversionName
     * @return array<string, mixed>
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    public function toStreamHeaders(string $conversionName): array
    {
        /** @var \Spatie\MediaLibrary\Filesystem\Filesystem $filesystem */
        if ($conversionName !== '') {
            $fileName = ConversionCollection::createForMedia($this)->getByName($conversionName);
            $fileName = $fileName->getConversionFile($this->file_name);
        } else {
            $fileName = $this->file_name;
        }

        $contentLength = Storage::size($this->toStreamPath($conversionName));

        return [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => $this->mime_type,
            'Content-Length' => $contentLength,
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'public',
        ];
    }

    public function toStreamPath(string $conversionName): string
    {
        /** @var \Spatie\MediaLibrary\Filesystem\Filesystem $filesystem */
        return $conversionName !== ''
            ? $this->getPath($conversionName)
            : $this->getPath();
    }
}
