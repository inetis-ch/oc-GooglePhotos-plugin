<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto image
 */
class Photo extends AbstractMedia
{
	public $photoTitle;
	public $photoUrl;
	public $thumbUrl;
	public $photoSummary;
	public $photoPublished;
	public $photoUpdated;
	public $imageHeight;
	public $imageWidth;

	/**
	 * @inheritDoc AbstractMedia
	 */
	public static function makeFromXml(SimpleXMLElement $entry, PicasaSettingsProviderInterface $settings)
	{
		$instance = new static();

		$namespaces = $entry->getNameSpaces(true);
		$gPhoto = $entry->children($namespaces['gphoto']);
		$media = $entry->children($namespaces['media']);
		$thumbnailAttr = $media->group->thumbnail->attributes();
		$photoAttr = $media->group->content->attributes();

		$instance->photoTitle = (string) $entry->title[0];
		$instance->photoUrl = (string) $photoAttr['url'];
		$instance->thumbUrl = (string) $thumbnailAttr['url'];
		$instance->photoSummary = (string) $entry->summary[0];
		$instance->photoPublished = (string) $entry->published;
		$instance->photoUpdated = (string) $entry->updated;
		$instance->imageHeight = (int) $gPhoto->height;
		$instance->imageWidth = (int) $gPhoto->width;

		$instance->thumbUrl = self::resizeImage(
			$instance->thumbUrl,
			$settings->getAlbumThumbSize(),
			$settings->getImagesCropMode(),
			$settings->getImagesShouldCrop()
		);

		return $instance;
	}
}
