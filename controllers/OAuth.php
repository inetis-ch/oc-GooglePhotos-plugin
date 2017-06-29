<?php namespace Inetis\GooglePhotos\Controllers;

use Backend;
use Backend\Classes\Controller;
use Exception;
use Flash;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider;
use Input;
use Lang;
use Redirect;
use Session;

class OAuth extends Controller
{
    public function callback()
    {
        // Check CSRF token
        if (Session::token() !== Input::get('state'))
        {
            abort(403, Lang::get('inetis.googlephotos::lang.messages.csrfMismatch'));
        }

        try
        {
            $settingsProvider = new SettingsProvider();
            $settingsProvider->exchangeToken(Input::get('code'));
            Flash::success(Lang::get('inetis.googlephotos::lang.messages.authSuccess'));
        }
        catch (Exception $e) {
            Flash::error($e->getMessage());
        }

        return Redirect::to(Backend::url('system/settings/update/inetis/googlephotos/settings'));
    }
}
