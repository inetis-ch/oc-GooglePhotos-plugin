<?php namespace Inetis\Googleimages\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'googleimages_settings';
    public $settingsFields = 'fields.yaml';


    public function initSettingsData()
    {
       
    }
}