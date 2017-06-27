<?php namespace Inetis\GooglePhotos\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\ComponentSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\PicasaClient;

class GoogleAlbum extends ComponentBase
{
    /**
     * @var PicasaClient
     */
    private $picasaClient;

    public function componentDetails()
    {
        return [
            'name' => 'Google Photos album',
            'description' => 'Display one album of the Google Photos account'
        ];
    }

    public function defineProperties()
    {
        return [
            'albumId' => [
                'title' => 'Album ID',
                'description' => 'ID of the Google Photos album to show',
                'default' => '{{ :albumId }}',
                'type' => 'string'
            ],
            'visibility' => [
                'title' => 'Visibility',
                'description' => 'The visibility level of the albums to show',
                'default' => 'all',
                'type' => 'dropdown',
                'options' => [ 'all' => 'All', 'public' => 'Public', 'private' => 'Private', 'visible' => 'Visible' ]
            ],
            'thumbSize' => [
                'title' => 'Thumbnail size',
                'description' => 'The height of the thumbnails to generate',
                'default' => '160',
                'type' => 'string'
            ],
            'shouldCrop' => [
                'title' => 'Square crop thumbnails',
                'description' => 'Whether to crop or just resize thumbnails',
                'default' => 1,
                'type' => 'dropdown',
                'options' => [ 0 => 'No', 1 => 'Yes' ]
            ],
            'cropMode' => [
                'title' => 'Crop mode',
                'description' => 'The dimension to use with "Thumbnail size" when resizing or cropping the thumbnails',
                'default' => 's',
                'type' => 'dropdown',
                'options' => [ 'h' => 'Height', 'w' => 'Width', 's' => 'Smallest', 'l' => 'Largest' ]
            ],
        ];
    }

    public function onRun()
    {
        $componentSettings = new ComponentSettingsProvider($this->properties);
        $token = $componentSettings->getOAuthToken();
        $this->picasaClient = new PicasaClient($componentSettings, $token);
    }

    public function images()
    {
        $albumId = $this->property('albumId');

        return Cache::remember(
            'picasaImages-' . $albumId,
            Settings::get('cacheDuration'),
            function() use($albumId) {
                return $this->picasaClient->getAlbumImages($albumId);
            }
        );
    }

}
