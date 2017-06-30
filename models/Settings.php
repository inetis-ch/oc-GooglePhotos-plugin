<?php namespace Inetis\GooglePhotos\Models;

use Model;

/**
 * Class Settings
 *
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'inetis_googlePhotos_settings';
    public $settingsFields = 'fields.yaml';


    public function initSettingsData()
    {
        foreach ($this->getDefaultSettings() as $key => $setting)
        {
            $this->{$key} = $setting;
        }
    }

    public function getDefaultSettings()
    {
        return [
            'cacheDuration' => 0,
            'hiddenAlbums' => [
                ['album' => 'Auto Backup']
            ]
        ];
    }
}
