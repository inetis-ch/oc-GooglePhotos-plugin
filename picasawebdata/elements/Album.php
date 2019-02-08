<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto album
 */
class Album extends AbstractMedia
{
    public $id;
    public $title;
    public $photoUrl;
    public $thumbUrl;
    public $photosCount;

    protected $legacyProperties = [
        'albumId' => 'id',
        'albumTitle' => 'title',
        'albumNumPhotos' => 'photosCount',
    ];

    /** @var PicasaSettingsProviderInterface */
    private $settings;

    /**
     * @inheritDoc AbstractMedia
     */
    public static function makeFromGooglePhotos(\stdClass $entry, PicasaSettingsProviderInterface $settings)
    {
        $instance = new static();
        $instance->settings = $settings;

        $instance->id          = $entry->id;
        $instance->title       = $entry->title;
        $instance->photosCount = $entry->mediaItemsCount;

        $instance->thumbUrl = $instance->photoUrl = self::getImageUrl(
            $entry->coverPhotoBaseUrl,
            $settings->getImagesWidth(),
            $settings->getImagesHeight(),
            $settings->getImagesShouldCrop()
        );

        return $instance;
    }

    /**
     * Check if the album belongs to the list of excluded albums.
     *
     * @return boolean
     */
    public function isExcluded()
    {
        $albumsToIgnore = $this->settings->getHiddenAlbums();

        return (
            in_array($this->id, $albumsToIgnore) ||
            in_array($this->title, $albumsToIgnore)
        );
    }
}
