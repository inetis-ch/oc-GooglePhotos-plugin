<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Exception;
use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\StoredTokenInterface;

class SettingsStoredToken implements StoredTokenInterface
{
    private $token;

    public function __construct()
    {
        $this->token = Settings::get('oAuth_token');

        if (is_null($this->token))
        {
            throw new Exception('Could not fetch OAuth token');
        }
    }

    public static function create($newToken)
    {
        self::checkTokenFormat($newToken, true);

        Settings::set('oAuth_token', $newToken);

        return new static();
    }

    public function getAccessToken()
    {
        return $this->token['access_token'];
    }

    public function getRefreshToken()
    {
        return $this->token['refresh_token'];
    }

    public function getExpirationTimestamp()
    {
        return $this->token['expiration'];
    }

    /**
     * Update and save the current instance with new token data.
     * This method is typically called when the OAuthToken is refreshed.
     *
     * @param array $newToken           The new data to use
     */
    public function updateToken($newToken)
    {
        self::checkTokenFormat($newToken, false);

        $this->token = array_merge($this->token, $newToken);
        Settings::set('oAuth_token', $this->token);
    }

    /**
     * Delete permanently the StoredToken.
     * This method is typically called when the OAuthToken is revoked.
     */
    public function deleteToken()
    {
        Settings::set('oAuth_token', null);
    }

    private static function checkTokenFormat(&$token, $isNewToken)
    {
        if (!isset($token['access_token']))
        {
            throw new Exception('Malformed OAuth token: missing access_token');
        }

        if (!isset($token['expires_in']))
        {
            throw new Exception('Malformed OAuth token: missing expires_in');
        }

        $token['expiration'] = time() + (int)$token['expires_in'];
        unset($token['expires_in']);

        // Updated tokens doesn't have all the fields of a new token
        if (!$isNewToken)
        {
            return;
        }

        if (!isset($token['refresh_token']))
        {
            throw new Exception('Malformed OAuth token: missing refresh_token');
        }
    }
}
