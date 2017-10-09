<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto video
 */
class Video extends AbstractMedia
{
	public $photoTitle;
	public $photoUrl;
	public $thumbUrl;
	public $photoSummary;
	public $photoPublished;
	public $photoUpdated;
	public $imageHeight;
	public $imageWidth;
	public $streams = [];

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

		foreach ($media->group->content as $stream)
		{
			$stream = ContentElement::makeFromXml($stream, $settings);

			if ($stream->isVideo())
				$instance->streams[] = $stream;
		}

		$instance->photoTitle = (string) $entry->title[0];
		$instance->photoUrl = self::getBestStream($instance->streams)->url;
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

	/**
	 * Get the url of the video stream with the highest resolution.
	 *
	 * @param ContentElement[] $streams
	 * @return ContentElement
	 */
	private static function getBestStream($streams)
	{
		$greatestStream = new ContentElement();

		foreach ($streams as $stream)
		{
			if ($stream->isBiggerThan($greatestStream))
			{
				$greatestStream = $stream;
			}
		}

		return $greatestStream;
	}
}
