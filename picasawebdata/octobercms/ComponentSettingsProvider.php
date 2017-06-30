<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

class ComponentSettingsProvider extends SettingsProvider implements PicasaSettingsProviderInterface
{
    private $properties;

    public function __construct($componentProperties)
    {
        parent::__construct();

        $this->properties = collect($componentProperties);
    }

    public function getVisibility()
    {
        return $this->properties->get('visibility') ?: 'public';
    }

    public function getAlbumThumbSize()
    {
        return $this->properties->get('thumbSize') ?: '160';
    }

    public function getImagesCropMode()
    {
        return $this->properties->get('cropMode') ?: 's';
    }

    public function getImagesShouldCrop()
    {
        $shouldCrop = $this->properties->get('shouldCrop');
        return (is_null($shouldCrop)) ? true : (bool) $shouldCrop;
    }

    public function getMaxResults()
    {
        return $this->properties->get('pageSize', 0);
    }

    public function getStartIndex()
    {
        return $this->properties->get('currentPage', 1) * $this->getMaxResults();
    }
}
