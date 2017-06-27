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
            'Inetis\GooglePhotos\Components\GoogleAlbums' => 'googleAlbums',
            'Inetis\GooglePhotos\Components\GoogleAlbum' => 'googleAlbum'
        ];
    }

    public function registerPageSnippets()
    {
        return [
            'Inetis\GooglePhotos\Components\GoogleAlbums' => 'googleAlbums',
            'Inetis\GooglePhotos\Components\GoogleAlbum' => 'googleAlbum'
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
