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

    public $settingsCode = 'googlePhotos_settings';
    public $settingsFields = 'fields.yaml';


    public function initSettingsData()
    {
       
    }
}
