<?php namespace Inetis\GooglePhotos\PicasaWebData;

use Exception;
use stdClass;
use SimpleXMLElement;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;
use Inetis\GooglePhotos\PicasaWebData\Base\Tokens\OAuthToken;
use October\Rain\Network\Http;

/**
 * Picasa Access and Google Photos interface class.
 * Using Picasa Web Data API v3.0
 *
 * @see https://developers.google.com/identity/protocols/OAuth2WebServer#callinganapi
 * @see https://developers.google.com/picasa-web/docs/3.0/developers_guide_protocol
 * @see https://developers.google.com/picasa-web/docs/3.0/reference
 */
class PicasaClient
{
    protected $settings;
    protected $token;

    /**
     * PicasaClient constructor.
     *
     * @param PicasaSettingsProviderInterface $settingsProvider
     * @param OAuthToken $token
     */
    public function __construct(PicasaSettingsProviderInterface $settingsProvider, OAuthToken $token)
    {
        $this->settings = $settingsProvider;
        $this->token = $token;
    }

    /**
     * Get the list of all albums on an account
     *
     * @return Elements\Album[]
     * @throws Exception
     */
    public function getAlbumsList()
    {
        $url = "https://picasaweb.google.com/data/feed/api/user/default";

        $http = Http::make($url, Http::METHOD_GET);
        $http->data([
            'kind' => 'album',
            'access' => $this->settings->getVisibility(),
            'thumbsize' => $this->settings->getAlbumThumbSize(),
            'deprecation-extension' => 'true'
        ]);
        $this->addPagination($http);
        $http->header([
            'Authorization' => 'Bearer ' . $this->token->getAccessToken(),
            'GData-Version' => '3',
            'Referer' => $this->settings->getHttpReferrer()
        ]);
        $http->send();

        if($http->code != 200)
            throw new Exception("Error trying to query Picasa API. Response status: " . $http->code);

        $albums = [];
        $xmlResult = $this->decodeXml($http->body);
        foreach ($xmlResult->entry as $entry)
        {
            $album = Elements\Album::makeFromXml($entry, $this->settings);

            if (!$album->isExcluded())
                $albums[] = $album;
        }

        return $albums;
    }

    /**
     * Get the images of an album by id
     *
     * @param string $albumId           A picasa album id
     * @param string $albumTitle        A reference that will be assigned as the title of the album fetched
     *
     * @return Elements\Photo[]
     * @throws Exception
     */
    public function getAlbumImages($albumId, &$albumTitle = "")
    {
        $url = "https://picasaweb.google.com/data/feed/api/user/default/albumid/" . $albumId;

        $http = Http::make($url, Http::METHOD_GET);
        $http->data([
            'thumbsize' => $this->settings->getAlbumThumbSize(),
            'access' => $this->settings->getVisibility(),
            'imgmax' => 'd',    // Retrieve original image (full size)
            'deprecation-extension' => 'true'
        ]);
        $this->addPagination($http);
        $http->header([
            'Authorization' => 'Bearer ' . $this->token->getAccessToken(),
            'GData-Version' => '3',
            'Referer' => $this->settings->getHttpReferrer()
        ]);
        $http->send();

        if($http->code != 200)
            throw new Exception("Error trying to query Picasa API. Response status: " . $http->code);

        $images = [];
        $xmlResult = $this->decodeXml($http->body);
        $albumTitle = (string) $xmlResult->title[0];
        foreach ($xmlResult->entry as $entry)
        {
            if (Elements\AbstractMedia::isVideo($entry))
            {
                $images[] = Elements\Video::makeFromXml($entry, $this->settings);
            }
            else
            {
                $images[] = Elements\Photo::makeFromXml($entry, $this->settings);
            }
        }

        return $images;
    }

    /**
     * Add pagination controls as query string to an url based on GData pagination format.
     *
     * @see https://developers.google.com/gdata/docs/2.0/reference#max-results
     * @see https://developers.google.com/gdata/docs/2.0/reference#start-index
     *
     * @param Http $http                An instance of http client which to append pagination parameters
     */
    private function addPagination(Http $http)
    {
        $startIndex = $this->settings->getStartIndex();
        $pageSize = $this->settings->getMaxResults();
        if ($startIndex)
        {
            $http->data($startIndex);
        }
        if ($pageSize)
        {
            $http->data($pageSize);
        }
    }

    /**
     * Decode an xml string with error handling
     *
     * @param string $response           The XML string received by the API
     *
     * @return SimpleXMLElement|stdClass
     */
    private function decodeXml($response)
    {
        libxml_use_internal_errors(true);
        try
        {
            $xml = new SimpleXMLElement($response);
        }
        catch (Exception $e)
        {
            $error_message = 'SimpleXMLElement threw an exception.';
            foreach(libxml_get_errors() as $error_line)
            {
                $error_message .= "\t" . $error_line->message;
            }
            trigger_error($error_message);
            return new stdClass();
        }
        return $xml;
    }
}
