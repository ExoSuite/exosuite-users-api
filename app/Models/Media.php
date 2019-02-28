<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Conversion\ConversionCollection;

/**
 * Class Media
 *
 * @package App\Models
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
            'Content-Disposition' => "attachment; filename='$fileName'",
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
