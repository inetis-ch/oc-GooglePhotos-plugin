<?php namespace Inetis\GooglePhotos\Controllers;

use Backend\Classes\Controller;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider;
use Input;
use Session;

class OAuth extends Controller
{
    public function callback()
    {
        // Check CSRF token
        if (Session::token() !== Input::get('state'))
        {
            abort(403, 'CSRF token mismatch');
        }

        $settingsProvider = new SettingsProvider();
        $settingsProvider->exchangeToken(Input::get('code'));
    }
}
