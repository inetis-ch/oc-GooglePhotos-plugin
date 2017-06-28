<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Tokens;

use Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider;
use October\Rain\Network\Http;

class OAuthToken
{
    protected $settings;
    protected $storedToken;
    protected $expirationOffset = 5;

    public function __construct(BaseSettingsProvider $settings, StoredTokenInterface $token = null)
    {
        $this->storedToken = is_null($token) ? $settings->getStoredToken() : $token;
        $this->settings = $settings;
    }

    public function getAccessToken($ensureValid = true)
    {
        if ($ensureValid)
            $this->refresh();

        return $this->storedToken->getAccessToken();
    }

    public function isExpired()
    {
        $expiration = $this->storedToken->getExpirationTimestamp();
        $expiration -= $this->expirationOffset;

        return $expiration < time();
    }

    public function refresh($force = false)
    {
        // Refresh the token only if it is expired or if force is enabled
        if (!$force && !$this->isExpired())
            return;

        $http = Http::make($this->settings->getTokenRenewUrl(), Http::METHOD_POST);
        $http->header([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);
        $http->data([
            'client_id' => $this->settings->getClientId(),
            'client_secret' => $this->settings->getClientSecret(),
            'refresh_token' => $this->storedToken->getRefreshToken(),
            'grant_type' => 'refresh_token'
        ]);
        $http->timeout(12);
        $http->send();

        $response = json_decode($http->body, true);
        $this->storedToken->updateToken($response);
    }
}
