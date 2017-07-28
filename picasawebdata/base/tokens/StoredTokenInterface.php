<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Tokens;

interface StoredTokenInterface
{
    // Accessors
    public function getAccessToken();
    public function getRefreshToken();
    public function getExpirationTimestamp();

    /**
     * Update and save the current instance with new token data.
     * This method is typically called when the OAuthToken is refreshed.
     *
     * @param array $newToken           The new data to use
     */
    public function updateToken($newToken);

    /**
     * Delete permanently the StoredToken.
     * This method is typically called when the OAuthToken is revoked.
     */
    public function deleteToken();
}
