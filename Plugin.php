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
            'name' => 'inetis.googlephotos::lang.plugin.name',
            'description' => 'inetis.googlephotos::lang.plugin.description',
            'author' => 'inetis',
            'icon' => 'icon-image'
        ];
    }

    public function registerComponents()
    {
        return [
            'Inetis\GooglePhotos\Components\GooglePhotosAlbums' => 'googlePhotosAlbums',
            'Inetis\GooglePhotos\Components\GooglePhotosAlbum' => 'googlePhotosAlbum'
        ];
    }

    public function registerPageSnippets()
    {
        return $this->registerComponents();
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
                'label' => 'inetis.googlephotos::lang.settings.menuEntry.label',
                'description' => 'inetis.googlephotos::lang.settings.menuEntry.description',
                'icon' => 'icon-image',
                'class' => 'Inetis\GooglePhotos\Models\Settings',
                'permissions' => ['inetis.googlephotos.access_settings'],
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'inetis.googlephotos.access_settings' => [
                'tab' => 'inetis.googlephotos::lang.permissions.tab',
                'label' => 'inetis.googlephotos::lang.permissions.access_settings'
            ],
        ];
    }

}
