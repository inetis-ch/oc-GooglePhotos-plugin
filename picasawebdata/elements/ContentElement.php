<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The object model of a GooglePhoto video stream or photo.
 */
class ContentElement
{
	public $url = '';
	public $height = 0;
	public $width = 0;
	public $contentType = null;
	protected $medium = null;

	/**
	 * Make stream representation from an XML <media:content> tag
	 * returned by the API.
	 *
	 * @param SimpleXMLElement $entry
	 * @param PicasaSettingsProviderInterface $settings
	 *
	 * @return AbstractMedia
	 */
	public static function makeFromXml(SimpleXMLElement $mediaContent, PicasaSettingsProviderInterface $settings)
	{
		$instance = new static();

		$attributes = $mediaContent->attributes();
		$instance->url = (string) $attributes['url'];
		$instance->height = (string) $attributes['height'];
		$instance->width = (string) $attributes['width'];
		$instance->contentType = (string) $attributes['type'];
		$instance->medium = (string) $attributes['medium'];

		return $instance;
	}

	/**
	 * Tell if the element dimensions are greater than an other
	 * element.
	 *
	 * @param ContentElement $otherContent
	 * @return boolean
	 */
	public function isBiggerThan(ContentElement $otherContent)
	{
		return $this->width > $otherContent->width;
	}

	/**
	 * Check if the element is a video stream
	 *
	 * @return boolean
	 */
	public function isVideo()
	{
		return $this->medium === 'video';
	}

	/**
	 * Check if the element is an image
	 *
	 * @return boolean
	 */
	public function isImage()
	{
		return $this->medium === 'image';
	}
}
