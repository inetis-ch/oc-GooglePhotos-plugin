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
            'name' => 'Insert image from Google',
            'description' => 'No description provided yet...',
            'author' => 'inetis',
            'icon' => 'icon-picture'
        ];
    }

    public function registerComponents()
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
                'label' => 'Google user id',
                'description' => 'Picasa Id of the user',
                'category' => 'Picasa',
                'default' => -1,
                'icon' => 'icon-cog',
                'class' => 'Inetis\GooglePhotos\Models\Settings',
                'order' => 500
            ]
        ];
    }

}
