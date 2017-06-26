<?php namespace Inetis\GooglePhotos\PicasaWebData\Base\Tokens;

interface StoredTokenInterface
{
    public function getAccessToken();
    public function getRefreshToken();
    public function getExpirationTimestamp();

    public function updateToken($newToken);
}
