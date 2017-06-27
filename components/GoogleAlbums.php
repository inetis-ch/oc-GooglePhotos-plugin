<?php namespace Inetis\GooglePhotos\Components;

use Cms\Classes\ComponentBase;
use Inetis\GooglePhotos\Models\Settings as PicasaSettings;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\ComponentSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\PicasaClient;

class GoogleAlbums extends ComponentBase
{
    /**
     * @var PicasaClient
     */
    private $picasaClient;

    public function componentDetails()
    {
        return [
            'name' => 'GoogleAlbums Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
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
            ]
        ];
    }

    public function onRun()
    {
        $componentSettings = new ComponentSettingsProvider($this->properties);
        $token = $componentSettings->getOAuthToken();
        $this->picasaClient = new PicasaClient($componentSettings, $token);
    }

    public function albums()
    {
        return $this->picasaClient->getAlbumsList();
    }
}
