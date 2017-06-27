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

    public function getImageMaxSize()
    {
        return $this->properties->get('imgSize') ?: '300';
    }
}
