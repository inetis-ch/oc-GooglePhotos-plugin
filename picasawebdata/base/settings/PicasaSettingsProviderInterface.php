<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Settings;

interface PicasaSettingsProviderInterface
{
    public function getHttpReferrer();
    public function getVisibility(); // A string being the visibility (see API Reference) of the albums to show
    public function getAlbumThumbSize(); // A string being the thumbsize (see API Reference) of the albums to show
    public function getImageMaxSize(); // A string being the imgmax (see API Reference) of the images to show
}
