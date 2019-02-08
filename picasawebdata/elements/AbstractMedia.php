<?php namespace Inetis\GooglePhotos\PicasaWebData\Elements;

use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

/**
 * The base class for elements, providing some common helpers.
 */
abstract class AbstractMedia
{
    /** @var array */
    protected $legacyProperties = [];

    /**
     * Get the name of the element type.
     *
     * @return string
     */
    public function type()
    {
        $className = get_class($this);
        $lastSlash = strrpos($className, '\\');

        if ($lastSlash)
        {
            $className = substr($className, $lastSlash + 1);
        }

        return strtolower($className);
    }

    /**
     * Make an element object model from an object returned by the GooglePhotos API.
     *
     * @param \stdClass $entry
     * @param PicasaSettingsProviderInterface $settings
     *
     * @return AbstractMedia
     */
    abstract public static function makeFromGooglePhotos(\stdClass $entry, PicasaSettingsProviderInterface $settings);

    /**
     * Check if a given media returned by the api is a video.
     *
     * @param \stdClass $entry
     *
     * @return boolean
     */
    public static function isVideo(\stdClass $entry)
    {
        return isset($entry->mediaMetadata) && isset($entry->mediaMetadata->video);
    }

    /**
     * Get a public url for an image.
     *
     * @see https://developers.google.com/photos/library/guides/access-media-items#base-urls
     *
     * @param string $baseUrl       The base url returned by the API
     * @param null $width           The requested width
     * @param null $height          The requested height
     * @param bool $crop            Should the image be cropped to the given width and height
     *
     * @return string
     */
    protected static function getImageUrl($baseUrl, $width = null, $height = null, $crop = false)
    {
        $parameters = [];

        // It is possible to crop only if both width and height are defined.
        // We square-crop if one of them is missing.
        if ($crop && ($width || $height)) {
            if (!$width) {
                $width = $height;
            }

            if (!$height) {
                $height = $width;
            }

            $parameters[] = 'c';
        }

        if ($width) {
            $parameters[] = 'w' . $width;
        }

        if ($height) {
            $parameters[] = 'h' . $height;
        }

        if (empty($parameters)) {
            return $baseUrl;
        }

        return $baseUrl . '=' . implode('-', $parameters);
    }

    /**
     * Accessors for Picasa API backward compatibility.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset($this->legacyProperties[$name])) {
            return null;
        }

        return $this->{$this->legacyProperties[$name]};
    }

    /**
     * Accessors for Picasa API backward compatibility.
     *
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return in_array($name, array_keys($this->legacyProperties));
    }
}
