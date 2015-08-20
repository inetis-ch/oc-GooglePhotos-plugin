<?php namespace Inetis\Googleimages;
use System\Classes\PluginBase;
/**
 * googleimages Plugin Information File
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
            'Inetis\Googleimages\Components\GoogleAlbums' => 'googleAlbums',
            'Inetis\Googleimages\Components\GoogleAlbum' => 'googleAlbum'
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
                'class' => 'Inetis\Googleimages\Models\Settings',
                'order' => 500
            ]
        ];
    }

}