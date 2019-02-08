<?php namespace Inetis\GooglePhotos\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\GooglePhotosClient;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\ComponentSettingsProvider;

class GooglePhotosAlbums extends ComponentBase
{
    /**
     * @var GooglePhotosClient
     */
    private $apiClient;

    private $albumsData = null;

    public function componentDetails()
    {
        return [
            'name' => 'inetis.googlephotos::lang.component.albums.name',
            'description' => 'inetis.googlephotos::lang.component.albums.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'albumPage' => [
                'title' => 'inetis.googlephotos::lang.component.fields.albumPageTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.albumPageDescription',
                'type' => 'dropdown',
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

    public function getAlbumPageOptions()
    {
        return Page::withComponent('googlePhotosAlbum')->sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $componentSettings = new ComponentSettingsProvider($this->properties);
        $token = $componentSettings->getOAuthToken();
        $this->apiClient = new GooglePhotosClient($componentSettings, $token);
        $this->loadData();
    }

    public function albums()
    {
        return $this->albumsData;
    }

    public function pagination()
    {

    }

    private function loadData()
    {
        $cacheKey = 'picasaAlbums';
        $cacheDuration = (int) Settings::get('cacheDuration');
        $cacheCallback = function() {
            return $this->apiClient->getAlbumsList();
        };

        if ($cacheDuration)
        {
            $result = Cache::remember($cacheKey, $cacheDuration, $cacheCallback);
        }
        else
        {
            $result = $cacheCallback();
        }

        $this->albumsData = collect($result);
    }
}
