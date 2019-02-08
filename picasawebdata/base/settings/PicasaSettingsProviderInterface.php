<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Settings;

interface PicasaSettingsProviderInterface
{
    public function getHttpReferrer();
    public function getImagesHeight(); // The max height (or height if cropping is enabled) of the thumbnails to generate
    public function getImagesWidth(); // The max width (or width if cropping is enabled) of the thumbnails to generate
    public function getImagesShouldCrop(); // A boolean, true if images should be cropped
    public function getMaxResults(); // An integer being the "max-result" parameter for pagination (see API Reference)
    public function getStartIndex(); // An integer being the "start-index" parameter for pagination (see API Reference)
    public function getHiddenAlbums();
}
