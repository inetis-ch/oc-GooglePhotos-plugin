<?php namespace Inetis\GooglePhotos\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Illuminate\Support\Collection;
use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\Elements\AbstractMedia;
use Inetis\GooglePhotos\PicasaWebData\Elements\Album;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\ComponentSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\GooglePhotosClient;

class GooglePhotosAlbum extends ComponentBase
{
    /** @var GooglePhotosClient */
    private $apiClient;

    /** @var Album */
    private $albumData = null;

    /** @var AbstractMedia[] */
    private $albumImages = [];

    public function componentDetails()
    {
        return [
            'name' => 'inetis.googlephotos::lang.component.album.name',
            'description' => 'inetis.googlephotos::lang.component.album.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'albumId' => [
                'title' => 'inetis.googlephotos::lang.component.fields.albumIdTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.albumIdDescription',
                'default' => '{{ :albumId }}',
                'type' => 'dropdown'
            ],
            /*'pageSize' => [
                'title' => 'inetis.googlephotos::lang.component.fields.pageSizeTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.pageSizeDescription',
                'default' => '0',
                'type' => 'string',
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.pagination'
            ],
            'currentPage' => [
                'title' => 'inetis.googlephotos::lang.component.fields.currentPageTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.currentPageDescription',
                'default' => '{{ :page }}',
                'type' => 'string',
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.pagination'
            ],*/
            'thumbHeight' => [
                'title' => 'inetis.googlephotos::lang.component.fields.thumbHeightTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.thumbHeightDescription',
                'default' => '160',
                'type' => 'string',
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
            'thumbWidth' => [
                'title' => 'inetis.googlephotos::lang.component.fields.thumbWidthTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.thumbWidthDescription',
                'default' => '160',
                'type' => 'string',
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
            'shouldCrop' => [
                'title' => 'inetis.googlephotos::lang.component.fields.shouldCropTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.shouldCropDescription',
                'default' => 0,
                'type' => 'dropdown',
                'options' => [
                    0 => 'inetis.googlephotos::lang.component.fields.optionNo',
                    1 => 'inetis.googlephotos::lang.component.fields.optionYes'
                ],
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
        ];
    }

    public function getAlbumIdOptions()
    {
        return collect($this->getClient()->getAlbumsList())
            ->mapWithKeys(function($item) {
                return ['id:' . $item->albumId => $item->albumTitle];
            })
            ->toArray();
    }

    public function onRun()
    {
        $this->apiClient = $this->getClient();
        $this->loadData();
    }

    public function album()
    {
        return $this->albumData;
    }

    public function images()
    {
        return $this->albumImages;
    }

    public function albumTitle()
    {
        return $this->albumData->title;
    }

    private function loadData()
    {
        $albumId = $this->property('albumId');

        if (is_null($albumId))
            abort(404);

        // The albumId may be passed as a string in format id:91827364546372819
        if (is_string($albumId) && ($separator = strpos($albumId, ':')) !== false) {
            $albumId = substr($albumId, $separator + 1);
        }

        $this->albumData = $this->getAlbumData($albumId);
        $this->albumImages = $this->getAlbumImages($albumId);
    }

    private function getAlbumImages(string $albumId) : Collection
    {
        $cacheKey = 'picasaImages-' . $albumId . '_';
        $cacheDuration = (int) Settings::get('cacheDuration');
        $cacheCallback = function() use($albumId) {
            return $this->apiClient->getAlbumImages($albumId);
        };

        if ($cacheDuration) {
            $result = Cache::remember($cacheKey, $cacheDuration, $cacheCallback);
        }
        else {
            $result = $cacheCallback();
        }

        return collect($result);
    }

    private function getAlbumData(string $albumId) : Album
    {
        $cacheKey = 'picasaAlbum-' . $albumId . '_';
        $cacheDuration = (int) Settings::get('cacheDuration');
        $cacheCallback = function() use($albumId) {
            return $this->apiClient->getAlbum($albumId);
        };

        if ($cacheDuration) {
            return Cache::remember($cacheKey, $cacheDuration, $cacheCallback);
        }

        return $cacheCallback();
    }

    private function getClient()
    {
        $componentSettings = new ComponentSettingsProvider($this->properties);
        $token = $componentSettings->getOAuthToken();
        return new GooglePhotosClient($componentSettings, $token);
    }
}
