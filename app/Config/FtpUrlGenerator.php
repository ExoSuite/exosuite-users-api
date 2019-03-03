<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 2019-01-30
 * Time: 13:41
 */

namespace App\Config;

use DateTimeInterface;
use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

/**
 * Class UrlGenerator
 *
 * @package App\Config
 */
class FtpUrlGenerator extends BaseUrlGenerator
{

    /**
     * Get the url for a media item.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return '';
    }

    /**
     * Get the temporary url for a media item.
     *
     * @param \DateTimeInterface $expiration
     * @param string[] $options
     * @return string
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return '';
    }

    /**
     * Get the url for the profile of a media item.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->getPathRelativeToRoot();
    }

    /**
     * Get the url to the directory containing responsive images.
     *
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        return '';
    }
}
