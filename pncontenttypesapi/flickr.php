<?php
/**
 * Content flickr plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


Loader::requireOnce('modules/content/pnincludes/phpFlickr/phpFlickr.php');


class content_contenttypesapi_FlickrPlugin extends contentTypeBase
{
  var $userName;
  var $tags;
  var $photoCount;

  function getModule() { return 'content'; }
  function getName() { return 'flickr'; }
  function getTitle() { return _CONTENT_CONTENTENTTYPE_FLICKRTITLE; }
  function getDescription() { return _CONTENT_CONTENTENTTYPE_FLICKRDESCR; }
  function getAdminInfo() { return _CONTENT_FLICKRAPIKEYLABELHELP; }

  function isActive()
  { 
    $apiKey = pnModGetVar('content', 'flickrApiKey');
    if (!empty($apiKey))
      return true; 
    return false;
  }
  
  
  function loadData($data)
  {
    $this->userName = $data['userName'];
    $this->tags = $data['tags'];
    $this->photoCount = $data['photoCount'];
  }

  
  function display()
  {
    $this->flickr = new phpFlickr(pnModGetVar('content', 'flickrApiKey'));

    $this->flickr->enableCache("fs", pnConfigGetVar('temp'));

    // Find the NSID of the username
    $person = $this->flickr->people_findByUsername($this->userName);
    
    // Get the photos
    //$photos = $this->flickr->people_getPublicPhotos($person['id'], NULL, $this->photoCount);
    $photos = $this->flickr->photos_search(array('user_id' => $person['id'],
                                                 'tags' => $this->tags,
                                                 'per_page' => $this->photoCount));

    $photoData = array();
    foreach ((array)$photos['photo'] as $photo)
    {
      $photoData[] = array('title' => DataUtil::formatForDisplayHTML($this->decode($photo['title'])),
                           'src'   => $this->flickr->buildPhotoURL($photo, "Square"),
                           'url'   => "http://www.flickr.com/photos/$photo[owner]/$photo[id]");
    }

    $render = pnRender::getInstance('content', false);
    $render->assign('photos', $photoData);

    return $render->fetch('contenttype/flickr_view.html');
  }

  
  function displayEditing()
  {
    return pnML('_CONTENT_CONTENTENTTYPE_FLICKREDITVIEW', 
                array('user' => $this->userName, 'tags' => $this->tags));
  }

  
  function getDefaultData()
  { 
    return array('userName' => '',
                 'tags' => '',
                 'photoCount' => 8);
  }


  function startEditing(&$render)
  {
    $render->assign('flickrApiKey', pnModGetVar('content', 'flickrApiKey'));
  }

  
  function decode($s)
  {
    return mb_convert_encoding($s, _CHARSET, 'UTF-8');
  }
}


function content_contenttypesapi_Flickr($args)
{
  return new content_contenttypesapi_FlickrPlugin($args['data']);
}

