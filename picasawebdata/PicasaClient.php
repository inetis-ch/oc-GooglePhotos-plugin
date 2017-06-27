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
     * @return array
     * @throws Exception
     */
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
                'photoUrl' => (string) $photoAttr['url'],
                'thumbUrl' => (string) $thumbnailAttr['url'],
                'albumId' => $albumId,
                'albumNumPhotos' => (string) $album->numphotos
            ];

            $albumData['thumbUrl'] = $this->resizeImage(
                $albumData['thumbUrl'],
                $this->settings->getAlbumThumbSize(),
                $this->settings->getImagesCropMode(),
                $this->settings->getImagesShouldCrop()
            );

            $albums[] = $albumData;
        }

        return $albums;
    }

    /**
     * Get the images of an album by id
     *
     * @param integer $albumId          A picasa album id
     * @param string $albumTitle        A reference that will be assigned as the title of the album fetched
     *
     * @return array
     * @throws Exception
     */
    public function getAlbumImages($albumId, &$albumTitle = "")
    {
        $url = "https://picasaweb.google.com/data/feed/api/user/default/albumid/" . $albumId
            . "?thumbsize=" . $this->settings->getAlbumThumbSize()
            . "&access=" . $this->settings->getVisibility();

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
        foreach ($xmlResult->entry as $entry)
        {
            $namespaces = $entry->getNameSpaces(true);
            $gPhoto = $entry->children($namespaces['gphoto']);
            $media = $entry->children($namespaces['media']);
            $thumbnailAttr = $media->group->thumbnail->attributes();
            $photoAttr = $media->group->content->attributes();

            $photoProperties = [
                'photoTitle' => (string) $entry->title[0],
                'photoUrl' => (string) $photoAttr['url'],
                'thumbUrl' => (string) $thumbnailAttr['url'],
                'photoSummary' => (string) $entry->summary[0],
                'photoPublished' => (string) $entry->published,
                'photoUpdated' => (string) $entry->updated,
                'imageHeight' => (int) $gPhoto->height,
                'imageWidth' => (int) $gPhoto->width
            ];

            $photoProperties['thumbUrl'] = $this->resizeImage(
                $photoProperties['thumbUrl'],
                $this->settings->getAlbumThumbSize(),
                $this->settings->getImagesCropMode(),
                $this->settings->getImagesShouldCrop()
            );

            $images[]    = $photoProperties;
        }
        return $images;
    }

    /**
     * This function rewrites the url of a thumbnail given by picasa API to change the crop mode.
     * This seems to be the only way to get custom cropping on thumbnails.
     *
     * @param string $imageUrl          The url generated by picasa API
     * @param string $requestedSize     The requested size used to generate $imageUrl
     * @param string $targetMode        The target cropping mode. Can be 'h' (height), 'w' (width), 's' (smallest), or 'l' (largest)
     * @param bool $targetCrop          Should the target image be cropped (or just resized if false)
     *
     * @return string
     */
    private function resizeImage($imageUrl, $requestedSize, $targetMode, $targetCrop)
    {
        $search = '/s'. $requestedSize .'-c/';

        $replace = '/' . $targetMode . $requestedSize;
        if ($targetCrop)
        {
            $replace .= '-c';
        }
        $replace .= '/';

        return str_replace($search, $replace, $imageUrl);
    }

    /**
     * Decode an xml string with error handling
     *
     * @param string$response           The XML string received by the API
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
