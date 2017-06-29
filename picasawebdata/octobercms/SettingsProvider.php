<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Config;
use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;
use Request;

class SettingsProvider extends BaseSettingsProvider
{
    public function __construct()
    {
        $this->tokenRedirectUrl = Request::root() . '/' . Config::get('cms.backendUri');
        $this->tokenRedirectUrl .= '/inetis/googlephotos/oauth/callback';

        $this->clientId = Config::get('inetis.googlephotos::clientId');
        $this->clientSecret = Config::get('inetis.googlephotos::clientSecret');
        $this->httpReferrer = Config::get('inetis.googlephotos::httpReferrer');
    }

    public function hasValidToken()
    {
        try {
            $oAuthToken = new OAuthToken($this);
            $oAuthToken->refresh(true);
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

    public function getStoredToken()
    {
        return new SettingsStoredToken();
    }

    public function setNewStoredToken($newToken, $state = null)
    {
        return SettingsStoredToken::create($newToken);
    }
}
