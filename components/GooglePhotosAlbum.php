<?php namespace Inetis\GooglePhotos\Components;

use Cache;
use Cms\Classes\ComponentBase;
use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\OctoberCms\ComponentSettingsProvider;
use Inetis\GooglePhotos\PicasaWebData\PicasaClient;

class GooglePhotosAlbum extends ComponentBase
{
    /**
     * @var PicasaClient
     */
    private $picasaClient;

    private $albumData = null;

    private $albumTitle;

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
                'type' => 'string'
            ],
            'visibility' => [
                'title' => 'inetis.googlephotos::lang.component.fields.visibilityTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.visibilityDescription',
                'default' => 'all',
                'type' => 'dropdown',
                'options' => [
                    'all' => 'inetis.googlephotos::lang.component.fields.optionAll',
                    'public' => 'inetis.googlephotos::lang.component.fields.optionPublic',
                    'private' => 'inetis.googlephotos::lang.component.fields.optionPrivate',
                    'visible' => 'inetis.googlephotos::lang.component.fields.optionVisible'
                ]
            ],
            'pageSize' => [
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
            ],
            'thumbSize' => [
                'title' => 'inetis.googlephotos::lang.component.fields.thumbSizeTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.thumbSizeDescription',
                'default' => '160',
                'type' => 'string',
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
            'cropMode' => [
                'title' => 'inetis.googlephotos::lang.component.fields.cropModeTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.cropModeDescription',
                'default' => 's',
                'type' => 'dropdown',
                'options' => [
                    'h' => 'inetis.googlephotos::lang.component.fields.optionHeight',
                    'w' => 'inetis.googlephotos::lang.component.fields.optionWidth',
                    's' => 'inetis.googlephotos::lang.component.fields.optionSmallest',
                    'l' => 'inetis.googlephotos::lang.component.fields.optionLargest'
                ],
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
            'shouldCrop' => [
                'title' => 'inetis.googlephotos::lang.component.fields.shouldCropTitle',
                'description' => 'inetis.googlephotos::lang.component.fields.shouldCropDescription',
                'default' => 1,
                'type' => 'dropdown',
                'options' => [
                    0 => 'inetis.googlephotos::lang.component.fields.optionNo',
                    1 => 'inetis.googlephotos::lang.component.fields.optionYes'
                ],
                'group' => 'inetis.googlephotos::lang.component.fieldsGroups.thumbnails'
            ],
        ];
    }

    public function onRun()
    {
        $componentSettings = new ComponentSettingsProvider($this->properties);
        $token = $componentSettings->getOAuthToken();
        $this->picasaClient = new PicasaClient($componentSettings, $token);
    }

    public function images()
    {
        $this->loadData();
        return $this->albumData;
    }

    public function albumTitle()
    {
        $this->loadData();
        return $this->albumTitle;
    }

    private function loadData()
    {
        if (!is_null($this->albumData))
            return;

        $albumId = $this->property('albumId');
        $cacheKey = 'picasaImages-' . $albumId . '_';
        $cacheDuration = (int) Settings::get('cacheDuration');
        $cacheCallback = function() use($albumId) {
            return $this->picasaClient->getAlbumImages($albumId, $this->albumTitle);
        };

        if ($cacheDuration)
        {
            $result = Cache::remember($cacheKey, $cacheDuration, $cacheCallback);
        }
        else
        {
            $result = $cacheCallback();
        }

        $this->albumData = collect($result);
    }
}