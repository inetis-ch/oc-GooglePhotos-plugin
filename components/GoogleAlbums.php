<?php namespace Inetis\GooglePhotos\Components;
use Cms\Classes\ComponentBase;
use Inetis\GooglePhotos\Models\Settings as PicasaSettings;
class GoogleAlbums extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'GoogleAlbums Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function onRun()
    {
       $this->userid = PicasaSettings::get('userid');
       $this->getAllAlbums();
    }

    private function getAllAlbums()
    {
        $url = "https://picasaweb.google.com/data/feed/base/"
                . "user/" . $this->userid;

        $this->page['albums'] = simplexml_load_file($url);
        
        foreach ($this->page['albums']->entry as $album)
        {    
            //extract image of the album
            $regexp = '@src="([^"]+)"@';
            preg_match_all($regexp, $album->summary, $result);    
            $album->image = str_replace('src="', '', $result[0][0]);
            
            //extract id of the album
            $url = parse_url($album->link[0]->attributes()->href);
            $tokens = explode('/', $url['path']);
            $album->albumid = $tokens[sizeof($tokens)-1];  
        }
    }
}
