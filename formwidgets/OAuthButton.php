<?php namespace Inetis\GooglePhotos\FormWidgets;

use Backend\Classes\WidgetBase;
use Exception;
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

    public function getSaveValue()
    {
        return null;
    }

    public function onLogout()
    {
        $settingsProvider = new SettingsProvider();
        $token = $settingsProvider->getOAuthToken();
        try
        {
            $token->revoke();
            Flash::success(Lang::get('inetis.googlephotos::lang.messages.revokeSuccess'));
        }
        catch (Exception $e)
        {
            Flash::error(Lang::get('inetis.googlephotos::lang.messages.revokeError', ['error' => $e->getMessage()]));
        }

        return [
            '#googlePhotosOAuthButton' => $this->render()
        ];
    }
}
