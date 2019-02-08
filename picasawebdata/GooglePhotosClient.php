<?php namespace Inetis\GooglePhotos\PicasaWebData;

use Exception;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;
use October\Rain\Network\Http;

/**
 * Client to access Google Photos library
 * Using Google Photos API v1
 *
 * @see https://developers.google.com/identity/protocols/OAuth2WebServer#callinganapi
 * @see https://developers.google.com/photos/library/guides/overview
 * @see https://developers.google.com/photos/library/reference/rest/
 */
class GooglePhotosClient
{
    const KEY_ALBUMS = 'albums';
    const KEY_IMAGES = 'mediaItems';
    const MAX_ITEMS_PER_PAGE = [
        self::KEY_ALBUMS => 50,
        self::KEY_IMAGES => 100,
    ];

    protected $settings;
    protected $token;

    /**
     * GooglePhotosClient constructor.
     *
     * @param PicasaSettingsProviderInterface $settingsProvider
     * @param OAuthToken                      $token
     */
    public function __construct(PicasaSettingsProviderInterface $settingsProvider, OAuthToken $token)
    {
        $this->settings = $settingsProvider;
        $this->token    = $token;
    }

    /**
     * Get the list of all albums on an account
     *
     * @return Elements\Album[]
     * @throws Exception
     */
    public function getAlbumsList() : array
    {
        $url = 'https://photoslibrary.googleapis.com/v1/albums';
        $http = Http::make($url, Http::METHOD_GET);
        $this->signRequest($http);

        $results = $this->fetchAllPages($http,self::KEY_ALBUMS);
        $albums = [];

        foreach ($results as $result)
        {
            $album = Elements\Album::makeFromGooglePhotos($result, $this->settings);

            if (!$album->isExcluded())
                $albums[] = $album;
        }

        return $albums;
    }

    /**
     * Get information about an album.
     *
     * @param string $albumId           A GooglePhotos album id
     *
     * @return Elements\Album
     * @throws Exception
     */
    public function getAlbum(string $albumId) : Elements\Album
    {
        $url = 'https://photoslibrary.googleapis.com/v1/albums/' . $albumId;
        $http = Http::make($url, Http::METHOD_GET);
        $this->signRequest($http);
        $http->send();

        if($http->code != 200)
            throw new Exception('Error trying to query Google Photos API. Response status: ' . $http->code);

        $result = json_decode($http->body);

        return Elements\Album::makeFromGooglePhotos($result, $this->settings);
    }

    /**
     * Get the images of an album by id
     *
     * @param string $albumId           A GooglePhotos album id
     *
     * @return Elements\Photo[]
     * @throws Exception
     */
    public function getAlbumImages(string $albumId) : array
    {
        $url = 'https://photoslibrary.googleapis.com/v1/mediaItems:search';
        $http = Http::make($url, Http::METHOD_POST);
        $this->signRequest($http);
        $http->data([
            'albumId' => $albumId
        ]);

        $results = $this->fetchAllPages($http,self::KEY_IMAGES);
        $images = [];

        foreach ($results as $result)
        {
            if (Elements\AbstractMedia::isVideo($result))
            {
                $images[] = Elements\Video::makeFromGooglePhotos($result, $this->settings);
            }
            else
            {
                $images[] = Elements\Photo::makeFromGooglePhotos($result, $this->settings);
            }
        }

        return $images;
    }

    private function signRequest(Http $http) : Http
    {
        $http->header([
            'Authorization' => 'Bearer ' . $this->token->getAccessToken(),
            'Referer' => $this->settings->getHttpReferrer()
        ]);

        return $http;
    }

    /**
     * Crawl all pages of a resource and return all results.
     *
     * @param Http $http                An authorized base request
     * @param string $groupKey          The key where to find the results in the response
     *
     * @return array
     * @throws Exception
     */
    private function fetchAllPages(Http $http, $groupKey)
    {
        $http->data('pageSize', self::MAX_ITEMS_PER_PAGE[$groupKey]);

        $nextPageToken = '';
        $results = [];

        do
        {
            $httpClone = clone $http;
            $httpClone->data('pageToken', $nextPageToken);
            $httpClone->send();

            if($httpClone->code != 200)
                throw new Exception('Error trying to query Google Photos API. Response status: ' . $httpClone->code);

            $pageResults = json_decode($httpClone->body);
            $nextPageToken = $pageResults->nextPageToken ?? null;
            $results = array_merge($results, $pageResults->$groupKey);
        }
        while (!empty($nextPageToken));

        return $results;
    }
}
