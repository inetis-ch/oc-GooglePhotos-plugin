<?php namespace Inetis\GooglePhotos\PicasaWebData\OctoberCms;

use Inetis\GooglePhotos\PicasaWebData\Base\Settings\PicasaSettingsProviderInterface;

class ComponentSettingsProvider extends SettingsProvider implements PicasaSettingsProviderInterface
{
    private $componentProperties;

    public function __construct($componentProperties)
    {
        parent::__construct();

        $this->componentProperties = $componentProperties;
    }

    public function getVisibility()
    {
        return $this->componentProperties['visibility'] ?: 'public';
    }

    public function getAlbumThumbSize()
    {
        return $this->componentProperties['thumbSize'] ?: '160';
    }

    public function getImageMaxSize()
    {
        return $this->componentProperties['imgSize'] ?: '300';
    }
}
