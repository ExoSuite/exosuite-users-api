<?php

namespace App\Models;

use App\Models\Traits\Uuids;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Filesystem\Filesystem;
use Spatie\MediaLibrary\Conversion\ConversionCollection;
use Illuminate\Support\Facades\Response;

/**
 * Class Media
 * @package App\Models
 */
class Media extends BaseMedia
{
    use Uuids;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @param string $conversionName
     * @return string
     */
    public function toStreamPath(string $conversionName)
    {
        /** @var Filesystem $filesystem */
        if ($conversionName !== '') {
            $path = $this->getPath($conversionName);
        }
        else {
            $path = $this->getPath();
        }

        return $path;
    }

    /**
     * @param string $conversionName
     * @return array
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversion
     */
    public function toStreamHeaders(string $conversionName)
    {
        /** @var Filesystem $filesystem */
        if ($conversionName !== '') {
            $fileName = ConversionCollection::createForMedia($this)->getByName($conversionName);
            $fileName = $fileName->getConversionFile($this->file_name);
        }
        else {
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
}
