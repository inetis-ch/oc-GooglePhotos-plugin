<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto album
 */
class Album extends AbstractMedia
{
	public $albumTitle;
	public $photoUrl;
	public $thumbUrl;
	public $albumId;
	public $albumNumPhotos;

	private $settings;

	/**
	 * @inheritDoc AbstractMedia
	 */
	public static function makeFromXml(SimpleXMLElement $entry, PicasaSettingsProviderInterface $settings)
	{
		$instance = new static();
		$instance->settings = $settings;

		$namespaces = $entry->getNameSpaces(true);
		$album = $entry->children($namespaces['gphoto']);
		$media = $entry->children($namespaces['media']);
		$thumbnailAttr = $media->group->thumbnail->attributes();
		$photoAttr = $media->group->content->attributes();

		$instance->albumTitle = (string) $entry->title;
		$instance->photoUrl = (string) $photoAttr['url'];
		$instance->thumbUrl = (string) $thumbnailAttr['url'];
		$instance->albumId = (string) $album->id;
		$instance->albumNumPhotos = (string) $album->numphotos;

		$instance->thumbUrl = self::resizeImage(
			$instance->thumbUrl,
			$settings->getAlbumThumbSize(),
			$settings->getImagesCropMode(),
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
			in_array($this->albumId, $albumsToIgnore) ||
			in_array($this->albumTitle, $albumsToIgnore)
		);
	}
}
