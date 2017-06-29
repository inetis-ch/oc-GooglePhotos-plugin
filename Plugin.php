<?php namespace Inetis\GooglePhotos;

use System\Classes\PluginBase;

/**
 * GooglePhotos Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Insert image from Google Photos',
            'description' => 'This plugin allows to display albums from your Google Photos (Picasa) account',
            'author' => 'inetis',
            'icon' => 'icon-image'
        ];
    }

    public function registerComponents()
    {
        return [
            'Inetis\GooglePhotos\Components\GoogleAlbums' => 'googlePhotosAlbums',
            'Inetis\GooglePhotos\Components\GoogleAlbum' => 'googlePhotosAlbum'
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'Inetis\GooglePhotos\Components\GoogleAlbums' => 'googlePhotosAlbums',
            'Inetis\GooglePhotos\Components\GoogleAlbum' => 'googlePhotosAlbum'
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Inetis\GooglePhotos\FormWidgets\OAuthButton' => [
                'label' => 'Location Selector',
                'code'  => 'googlePhotosOAuthButton'
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Google Photos',
                'description' => 'Settings for Google Photos plugin',
                'icon' => 'icon-image',
                'class' => 'Inetis\GooglePhotos\Models\Settings',
            ]
        ];
    }

}
