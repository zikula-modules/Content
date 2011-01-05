<?php
/**
 * Content flickr plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

require_once 'modules/Content/includes/phpFlickr/phpFlickr.php';

class content_contenttypesapi_FlickrPlugin extends contentTypeBase
{
    var $userName;
    var $tags;
    var $photoCount;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'flickr';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Photos from Flickr.com', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display thumbnails from specific Flickr user or tags.', $dom);
    }
    function getAdminInfo()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('If your want to add Flickr photos to your content then you need a Flickr API key. You can get this from <a href="http://www.flickr.com/api">flickr.com</a>.', $dom);
    }
    function isActive()
    {
        $apiKey = $this->getVar('flickrApiKey');
        if (!empty($apiKey)) {
            return true;
        }
        return false;
    }
    function loadData(&$data)
    {
        $this->userName = $data['userName'];
        $this->tags = $data['tags'];
        $this->photoCount = $data['photoCount'];
    }
    function display()
    {
        $this->flickr = new phpFlickr($this->getVar('flickrApiKey'));
        $this->flickr->enableCache("fs", System::getVar('temp'));

        // Find the NSID of the username
        $person = $this->flickr->people_findByUsername($this->userName);

        // Get the photos
        //$photos = $this->flickr->people_getPublicPhotos($person['id'], NULL, $this->photoCount);
        $photos = $this->flickr->photos_search(array('user_id' => $person['id'], 'tags' => $this->tags, 'per_page' => $this->photoCount));

        $photoData = array();
        foreach ((array) $photos['photo'] as $photo) {
            $photoData[] = array('title' => DataUtil::formatForDisplayHTML($this->decode($photo['title'])), 'src' => $this->flickr->buildPhotoURL($photo, "Square"), 'url' => "http://www.flickr.com/photos/$photo[owner]/$photo[id]");
        }

        $view = Zikula_View::getInstance('Content', false);
        $view->assign('photos', $photoData);

        return $view->fetch('contenttype/flickr_view.html');
    }
    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __f('Flickr photos from user %1$s associated with tags %2$s', array($this->userName, $this->tags), $dom);
    }
    function getDefaultData()
    {
        return array('userName' => '', 'tags' => '', 'photoCount' => 8);
    }
    function startEditing(&$view)
    {
        $view->assign('flickrApiKey', $this->getVar('flickrApiKey'));
    }
    function decode($s)
    {
        return mb_convert_encoding($s, mb_detect_encoding($s), 'UTF-8');
    }
}

function content_contenttypesapi_Flickr($args)
{
    return new content_contenttypesapi_FlickrPlugin($args['data']);
}

