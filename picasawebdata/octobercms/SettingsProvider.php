<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Backend;
use Config;
use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\StoredTokenInterface;

class SettingsProvider extends BaseSettingsProvider
{
    /**
     * SettingsProvider constructor.
     */
    public function __construct()
    {
        $this->tokenRedirectUrl = Backend::url('inetis/googlephotos/oauth/callback');
        $this->clientId = Config::get('inetis.googlephotos::clientId');
        $this->clientSecret = Config::get('inetis.googlephotos::clientSecret');
        $this->httpReferrer = Config::get('inetis.googlephotos::httpReferrer');
    }

    /**
     * This method check if a OAuth token is valid by forcing a refresh on it.
     *
     * @return bool
     */
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

    /**
     * Get an instantiated StoredToken.
     *
     * @return StoredTokenInterface
     */
    public function getStoredToken()
    {
        return new SettingsStoredToken();
    }

    /**
     * Save a new token received by an OAuth server.
     *
     * @param mixed $newToken           The raw token received by the OAuth server
     * @param string $state             The state passed trough the OAuth flow
     *
     * @return StoredTokenInterface
     */
    public function setNewStoredToken($newToken, $state = null)
    {
        return SettingsStoredToken::create($newToken);
    }
}
