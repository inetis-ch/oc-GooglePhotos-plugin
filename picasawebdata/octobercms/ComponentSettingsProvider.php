<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Inetis\GooglePhotos\Models\Settings;
use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

class ComponentSettingsProvider extends SettingsProvider implements PicasaSettingsProviderInterface
{
    private $properties;

    public function __construct($componentProperties)
    {
        parent::__construct();

        $this->properties = collect($componentProperties);
    }

    public function getImagesHeight()
    {
        return $this->properties->get('thumbHeight');
    }

    public function getImagesWidth()
    {
        return $this->properties->get('thumbWidth');
    }

    public function getImagesShouldCrop()
    {
        return (bool) $this->properties->get('shouldCrop', false);
    }

    public function getMaxResults()
    {
        return $this->properties->get('pageSize', 0);
    }

    public function getStartIndex()
    {
        return $this->properties->get('currentPage', 1) * $this->getMaxResults();
    }

    public function getHiddenAlbums()
    {
        $hiddenAlbums = Settings::get('hiddenAlbums', []);
        return $hiddenAlbums = collect($hiddenAlbums)->flatten()->toArray();
    }
}
