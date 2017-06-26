<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Settings;

use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;

abstract class BaseSettingsProvider
{
    protected $clientId = "663112926540-dvupotbji98icmhet7vjaehbfekbha9s.apps.googleusercontent.com";
    protected $clientSecret = "eNyOZ0AylFkKbqRylZYVHWLS";
    protected $httpReferrer = "";

    protected $tokenRenewUrl = "https://www.googleapis.com/oauth2/v3/token";
    protected $tokenRequestUrl = "https://accounts.google.com/o/oauth2/v2/auth";
    protected $tokenExchangeUrl = "https://www.googleapis.com/oauth2/v4/token";
    protected $tokenRevokeUrl = "https://accounts.google.com/o/oauth2/revoke";
    protected $tokenScopes = [
        "https://picasaweb.google.com/data"
    ];
    protected $tokenRedirectUrl = "";

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

    public function exchangeToken($code, $tokenId = null)
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

        $this->setNewStoredToken($body, $tokenId);
    }

    public function buildRevokeUrl()
    {
        $token = new OAuthToken($this, $this->getStoredToken());

        $url = $this->tokenRevokeUrl;
        $url .= "?token=" . $token->getAccessToken();

        return $url;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getHttpReferrer()
    {
        return $this->httpReferrer;
    }

    public function getTokenRenewUrl()
    {
        return $this->tokenRenewUrl;
    }

    public function getTokenRequestUrl()
    {
        return $this->tokenRequestUrl;
    }

    abstract public function getStoredToken(); // Return an object that implements StoredTokenInterface

    abstract public function setNewStoredToken($newToken, $state);
}
