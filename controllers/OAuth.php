<?php namespace Inetis\GooglePhotos\Controllers;

use Backend;
use Backend\Classes\Controller;
use Exception;
use Flash;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider;
use Input;
use Redirect;
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

        try
        {
            $settingsProvider = new SettingsProvider();
            $settingsProvider->exchangeToken(Input::get('code'));
            Flash::success('Authenticated successfully');
        }
        catch (Exception $e) {
            Flash::error($e->getMessage());
        }

        return Redirect::to(Backend::url('system/settings/update/inetis/googlephotos/settings'));
    }
}
