<?php namespace Inetis\GooglePhotos\FormWidgets;

use Backend\Classes\WidgetBase;
use Flash;
use Http;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\SettingsProvider;
use Lang;

class OAuthButton extends WidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'googlePhotosOAuthButton';

    public function render()
    {
        return $this->makePartial('oauthbutton');
    }

    public function onLogout()
    {
        $settingsProvider = new SettingsProvider();
        $logoutRequest = Http::get($settingsProvider->buildRevokeUrl());
        $response = json_decode($logoutRequest->body);

        if ($logoutRequest->code === 200)
        {
            Flash::success(Lang::get('inetis.googlephotos::lang.messages.revokeSuccess'));
        }
        else
        {
            Flash::error(Lang::get('inetis.googlephotos::lang.messages.revokeError', ['error' => $response->error_description]));
        }

        return [
            '#googlePhotosOAuthButton' => $this->render()
        ];
    }
}
