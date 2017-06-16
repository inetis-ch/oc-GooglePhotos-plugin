<?php namespace Inetis\GooglePhotos\Components;
use Cms\Classes\ComponentBase;
use Inetis\GooglePhotos\Models\Settings as PicasaSettings;
class GoogleAlbum extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Display one album',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [
            'albumId' => [
                'title' => 'Album ID',
                'description' => 'ID of the picasa album',
                'default'     => '{{ :albumId }}',
                'type' => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->userid = PicasaSettings::get('userid');
        $this->getOneAlbum($this->property('albumId'));
    }

    private function getOneAlbum($albumId)
    {
        $url = "https://picasaweb.google.com/data/feed/base/"
                . "user/" . $this->userid
                . "/albumid/" . $albumId
                . "?alt=rss&kind=photo&thumbsize=220";

        $this->page['album'] = simplexml_load_file($url);
        
        foreach ($this->page['album']->channel->item as $album)
        { 
            //extract image of the album
            $regexp = '@src="([^"]+)"@';
            preg_match_all($regexp, $album->description, $result);    
            $album->image = str_replace('src="', '', $result[0][0]);   
        }
        
        /*echo '<pre>';
        print_r($this->page['album']);
        echo '</pre>';
         */
    }

}
