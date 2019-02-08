<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use Carbon\Carbon;
use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto video
 */
class Video extends AbstractMedia
{
    public $filename;
    public $description;
    public $url;
    public $thumbUrl;
    public $mimeType;
    public $creationTime;
    public $width;
    public $height;
    public $fps;
    public $status;

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
        $instance->fps = data_get($entry, 'mediaMetadata.video.fps');
        $instance->status = data_get($entry, 'mediaMetadata.video.status');

        $instance->thumbUrl = self::getImageUrl(
            $entry->baseUrl,
            $settings->getImagesWidth(),
            $settings->getImagesHeight(),
            $settings->getImagesShouldCrop()
        );

        $instance->url = $entry->baseUrl . '=dv';

        return $instance;
    }
}
