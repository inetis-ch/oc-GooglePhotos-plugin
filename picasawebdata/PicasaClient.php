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

    public function __construct(PicasaSettingsProviderInterface $settingsProvider, OAuthToken $token)
    {
        $this->settings = $settingsProvider;
        $this->token = $token;
    }

    public function getAlbumsList()
    {
        $url = "https://picasaweb.google.com/data/feed/api/user/default"
            . "?kind=album"
            . "&access=" . $this->settings->getVisibility()
            . "&thumbsize=" . $this->settings->getAlbumThumbSize();

        $http = Http::make($url, Http::METHOD_GET);
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
        $namespaces = $xmlResult->getDocNamespaces();
        foreach ($xmlResult->entry as $entry)
        {
            $album = $entry->children($namespaces['gphoto']);
            $albumId = (string) $album->id;
            $media = $entry->children($namespaces['media']);
            $thumbnailAttr = $media->group->thumbnail->attributes();
            $photoAttr = $media->group->content->attributes();
            $albumData = [
                'albumTitle' => (string) $entry->title,
                'photoURL' => (string) $photoAttr['url'],
                'thumbURL' => (string) $thumbnailAttr['url'],
                'albumID' => $albumId,
                'albumNumPhotos' => (string) $album->numphotos
            ];
            $albums[] = $albumData;
        }

        return $albums;
    }

    public function getAlbumImages($albumId, &$albumTitle = "")
    {
        $url = "https://picasaweb.google.com/data/feed/api/user/default/albumid/" . $albumId
            . "?thumbsize=" . $this->settings->getAlbumThumbSize()
            . "&imgmax=" . $this->settings->getImageMaxSize();

        $http = Http::make($url, Http::METHOD_GET);
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
        $namespaces = $xmlResult->getDocNamespaces();
        foreach ($xmlResult->entry as $entry)
        {
            $namespaces = $entry->getNameSpaces(true);
            $gPhoto = $entry->children($namespaces['gphoto']);

            $album = $entry->children($namespaces['gphoto']);
            $media = $entry->children($namespaces['media']);
            $thumbnailAttr = $media->group->thumbnail->attributes();
            $photoAttr = $media->group->content->attributes();

            //(string) $thumbnailAttr['url'] = preg_replace("/\/s" . $thumbnailSize . "-c\//", "/h" . $thumbnailSize . "/", (string) $tTumbnailAttr['url']);

            $photoProperties = [
                'photoTitle' => (string) $entry->title[0],
                'photoURL' => (string) $photoAttr['url'],
                'thumbURL' => (string) $thumbnailAttr['url'],
                'photoSummary' => (string) $entry->summary[0],
                'photoPublished' => (string) $entry->published,
                'photoUpdated' => (string) $entry->updated,
                'imageHeight' => (int) $gPhoto->height,
                'imageWidth' => (int) $gPhoto->width
            ];
            $images[]    = $photoProperties;
        }
        return $images;
    }

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
