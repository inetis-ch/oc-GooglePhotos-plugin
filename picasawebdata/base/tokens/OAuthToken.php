<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Tokens;

use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\BaseSettingsProvider;
use October\Rain\Network\Http;

class OAuthToken
{
    protected $settings;
    protected $storedToken;
    protected $expirationOffset = 5;
    protected $isRevoked = false;

    /**
     * OAuthToken constructor.
     * The $token parameter can be omitted if $settings->getStoredToken() can be called without parameter.
     *
     * @param BaseSettingsProvider $settings
     * @param StoredTokenInterface|null $token
     */
    public function __construct(BaseSettingsProvider $settings, StoredTokenInterface $token = null)
    {
        $this->storedToken = is_null($token) ? $settings->getStoredToken() : $token;
        $this->settings = $settings;
    }

    /**
     * Get the access token that is used to authenticate your requests to the OAuth protected resources.
     *
     * @param bool $ensureValid                 If set to false, the expiration date of the token will not be checked
     *
     * @return string
     */
    public function getAccessToken($ensureValid = true)
    {
        if ($ensureValid)
            $this->refresh();

        return $this->storedToken->getAccessToken();
    }

    /**
     * Check if the current token is expired based on the expiration date of the StoredToken.
     * Note that it doesn't guarantee that the token is valid.
     *
     * @return bool
     */
    public function isExpired()
    {
        $expiration = $this->storedToken->getExpirationTimestamp();
        $expiration -= $this->expirationOffset;

        return $expiration < time();
    }

    /**
     * Check if the revoke action was run successfully on this token.
     * This is useful for some OAuth servers such as Google ones on which
     * there is a small delay after the revoke order is sent where it is
     * still possible to refresh the token.
     *
     * @return bool
     */
    public function isRevoked()
    {
        return $this->isRevoked;
    }

    /**
     * Refresh the token when it is expired. This method is called automatically by
     * getAccessToken() when its $ensureValid parameter true (the default).
     *
     * @param bool $force                       If true, a refresh request will be issued even if the accessToken is not expired
     * @throws Exception
     */
    public function refresh($force = false)
    {
        // Refresh the token only if it is expired or if force is enabled
        if (!$force && !$this->isExpired())
            return;

        // Don't allow refresh if we know that the token was revoked
        if ($this->isRevoked())
            throw new Exception('Could not refresh authorization token: it was already revoked');

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

        if ($http->code !== 200)
            throw new Exception('Could not refresh authorization token: server response code was ' . $http->code);

        $response = json_decode($http->body, true);
        $this->storedToken->updateToken($response);
    }

    /**
     * Ask the OAuth server to revoke the token.
     * This OAuthToken instance will not be usable anymore if the operation is successful.
     *
     * @param bool $delete                      If false, the revoked StoredToken will not be deleted
     *
     * @return mixed        The response body received from the OAuth server
     * @throws Exception    When the OAuth server respond with an error
     */
    public function revoke($delete = true)
    {
        $logoutRequest = Http::get($this->settings->buildRevokeUrl($this));
        $response = json_decode($logoutRequest->body);

        if ($logoutRequest->code === 200)
        {
            if ($delete)
            {
                $this->storedToken->deleteToken();
            }

            $this->isRevoked = true;
            return $response;
        }

        throw new Exception($response->error_description, $logoutRequest->code);
    }
}
