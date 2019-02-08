<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use Carbon\Carbon;
use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto image
 */
class Photo extends AbstractMedia
{
    public $filename;
    public $description;
    public $url;
    public $thumbUrl;
    public $mimeType;
    public $creationTime;
    public $width;
    public $height;

    protected $legacyProperties = [
        'photoTitle' => 'filename',
        'photoSummary' => 'description',
        'photoPublished' => 'creationTime',
        'photoUpdated' => 'creationTime',
        'imageHeight' => 'height',
        'imageWidth' => 'width',
        'photoUrl' => 'url',
    ];

    /**
     * @inheritDoc AbstractMedia
     */
    public static function makeFromGooglePhotos(\stdClass $entry, PicasaSettingsProviderInterface $settings)
    {
        $instance = new static();

        $instance->filename = data_get($entry, 'filename');
        $instance->description = data_get($entry, 'description');
        $instance->mimeType = data_get($entry, 'mimeType');
        $instance->width = data_get($entry, 'mediaMetadata.width');
        $instance->height = data_get($entry, 'mediaMetadata.height');
        $instance->creationTime = new Carbon(data_get($entry, 'mediaMetadata.creationTime'));

        $instance->url = self::getImageUrl(
            $entry->baseUrl,
            $instance->width,
            $instance->height,
            false
        );

        $instance->thumbUrl = self::getImageUrl(
            $entry->baseUrl,
            $settings->getImagesWidth(),
            $settings->getImagesHeight(),
            $settings->getImagesShouldCrop()
        );

        return $instance;
    }
}
