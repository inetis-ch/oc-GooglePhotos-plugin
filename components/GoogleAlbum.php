<?php namespace Inetis\GooglePhotos\Components;

use Cms\Classes\ComponentBase;
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
            'name' => 'Display one album',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'albumId' => [
                'title' => 'Album ID',
                'description' => 'ID of the picasa album',
                'default'     => '{{ :albumId }}',
                'type' => 'string'
            ],
            'visibility' => [
                'title' => 'visibility',
                'description' => 'The visibility level of the albums to show',
                'default' => 'all',
                'type' => 'dropdown',
                'options' => [ 'all' => 'All', 'public' => 'Public', 'private' => 'Private', 'visible' => 'Visible' ]
            ],
            'thumbSize' => [
                'title' => 'Thumbnail size',
                'description' => 'The height of the thumbnails to generate',
                'default' => '160',
                'type' => 'text'
            ],
            'imgSize' => [
                'title' => 'Image size',
                'description' => 'The height of the images to display',
                'default' => '300',
                'type' => 'text'
            ]
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
        return $this->picasaClient->getAlbumImages($albumId);
    }

}
