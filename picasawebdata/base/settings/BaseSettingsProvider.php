<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Settings;

use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\StoredTokenInterface;

abstract class BaseSettingsProvider
{
    protected $clientId = "";
    protected $clientSecret = "";
    protected $httpReferrer = "";

    protected $tokenRenewUrl = "https://www.googleapis.com/oauth2/v3/token";
    protected $tokenRequestUrl = "https://accounts.google.com/o/oauth2/v2/auth";
    protected $tokenExchangeUrl = "https://www.googleapis.com/oauth2/v4/token";
    protected $tokenRevokeUrl = "https://accounts.google.com/o/oauth2/revoke";
    protected $tokenScopes = [
        "https://www.googleapis.com/auth/photoslibrary.readonly"
    ];
    protected $tokenRedirectUrl = "";

    /**
     * Build an url that can be called by the user browser to get to the login screen.
     *
     * @param string $state             An arbitrary parameter that you can use to store data that will be passed all along the flow
     *
     * @return string
     */
    public function buildAuthUrl($state = null)
    {
        $url = $this->tokenRequestUrl;
        $url .= "?scope=" . urlencode(implode(" ", $this->tokenScopes));
        $url .= "&access_type=offline";
        $url .= "&include_granted_scopes=true";
        $url .= "&redirect_uri=" . str_replace('&', '%26', $this->tokenRedirectUrl);
        $url .= "&response_type=code";
        $url .= "&client_id=" . $this->clientId;

        if (!is_null($state))
        {
            $url .= "&state=" . $state;
        }

        return $url;
    }

    /**
     * Call the OAuth server to exchange a code with a token
     *
     * @param string $code              The code obtained after the user has granted access
     * @param string $state             The state that was passed from the beginning of the flow to the buildAuthUrl() method
     *
     * @throws Exception
     */
    public function exchangeToken($code, $state = null)
    {
        // Don't use October's Http client here because Google API wants a redirect_uri with special encoding
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->tokenExchangeUrl);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        $postBody = "code=$code";
        $postBody .= "&grant_type=authorization_code";
        $postBody .= "&redirect_uri=" . str_replace('&', '%26', $this->tokenRedirectUrl);
        $postBody .= "&client_id=" . $this->clientId;
        $postBody .= "&client_secret=" . $this->clientSecret;

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postBody);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);

        $response   = curl_exec($curl);
        $info       = curl_getinfo($curl);
        $headerSize = $info['header_size'];
        $status     = $info['http_code'];
        $body       = json_decode(substr($response, $headerSize), true);

        if ($status !== 200)
        {
            throw new Exception("Could not exchange authorization code with oAuth server");
        }

        $this->setNewStoredToken($body, $state);
    }

    /**
     * Get an url that can be called to revoke an OAuth token
     *
     * @param OAuthToken $token
     *
     * @return string
     */
    public function buildRevokeUrl(OAuthToken $token)
    {
        $url = $this->tokenRevokeUrl;
        $url .= "?token=" . $token->getAccessToken();

        return $url;
    }

    /**
     * Get the "client_id" or "app_id" of your OAuth app.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the "client_secret" of your OAuth app.
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Currently not used in Google OAuth flow, it should return a http referrer to pass along with the request.
     *
     * @return string
     */
    public function getHttpReferrer()
    {
        return $this->httpReferrer;
    }

    /**
     * Get the base url used to renew an expired token
     *
     * @return string
     */
    public function getTokenRenewUrl()
    {
        return $this->tokenRenewUrl;
    }

    /**
     * Get the base url used to display the authentication screen to the end user.
     *
     * @return string
     */
    public function getTokenRequestUrl()
    {
        return $this->tokenRequestUrl;
    }

    /**
     * This method instantiate and returns an OAuthToken object using the current SettingsProvider.
     * If arguments are passed to this method, they will be passed as is to the getStoredToken() method.
     *
     * @return OAuthToken
     */
    public function getOAuthToken()
    {
        $arguments = func_get_args();
        return new OAuthToken($this, $this->getStoredToken(...$arguments));
    }

    /**
     * Get an instantiated StoredToken. This method may be called with a variable number of parameters.
     * When overriding this method, you may pass some parameters (for example if your StoredToken have to load one out of many tokens).
     * If you don't need parameters to instantiate a StoredToken, then you will be able to omit the OAuthToken object when instantiating
     * a class with a SettingsProvider (for example the OAuthToken class).
     *
     * @return StoredTokenInterface
     */
    abstract public function getStoredToken();

    /**
     * This method should save a new token received by an OAuth server. It is called by the exchangeToken() method.
     *
     * @param mixed $newToken           The raw token received by the OAuth server
     * @param string $state             The state passed trough the OAuth flow from the beginning of the process when calling buildAuthUrl()
     *
     * @return StoredTokenInterface
     */
    abstract public function setNewStoredToken($newToken, $state);
}
