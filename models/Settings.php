<?php namespace Inetis\GooglePhotos\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'googlePhotos_settings';
    public $settingsFields = 'fields.yaml';


    public function initSettingsData()
    {
       
    }
}
