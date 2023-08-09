<?php

use Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider;

Route::get('/inetis/googlephotos/oauth/callback', function () {

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

})->middleware('web');
