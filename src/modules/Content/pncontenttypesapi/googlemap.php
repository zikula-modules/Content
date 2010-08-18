<?php
/**
 * Content google map plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_googlemapPlugin extends contentTypeBase
{
    var $longitude;
    var $latitude;
    var $zoom;
    var $height;
    var $text;
    var $infotext;
    var $streetviewcontrol;
    var $directionslink;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'googlemap';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Google map', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display Google map position.', $dom);
    }
    function getAdminInfo()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('If you want to add Google maps to your content then you need a Google maps API key. You can get this from <a href="http://code.google.com/apis/maps/signup.html">google.com</a>.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function isActive()
    {
        $apiKey = ModUtil::getVar('Content', 'googlemapApiKey');
        if (!empty($apiKey)) {
            return true;
        }
        return false;
    }
    function loadData(&$data)
    {
        $this->longitude = $data['longitude'];
        $this->latitude = $data['latitude'];
        $this->zoom = $data['zoom'];
        $this->height = $data['height'];
        $this->text = $data['text'];
        $this->infotext = $data['infotext'];
        $this->streetviewcontrol = $data['streetviewcontrol'];
        $this->directionslink = $data['directionslink'];
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('latitude', $this->latitude);
        $view->assign('longitude', $this->longitude);
        $view->assign('zoom', $this->zoom);
        $view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $view->assign('text', $this->text);
        $view->assign('height', $this->height);
        $view->assign('infotext', $this->infotext);
        $view->assign('streetviewcontrol', $this->streetviewcontrol);
        $view->assign('directionslink', $this->directionslink);
        $view->assign('googlemapApiKey', ModUtil::getVar('Content', 'googlemapApiKey'));
        $view->assign('contentId', $this->contentId);
        $view->assign('language', ZLanguage::getLanguageCode());

        return $view->fetch('contenttype/googlemap_view.html');
    }
    function displayEditing()
    {
        return DataUtil::formatForDisplay($this->text);
    }
    function getDefaultData()
    {
        return array(
            'longitude' => '12.36185073852539',
            'latitude' => '55.8756960390043',
            'zoom' => 5,
            'height' => '400',
            'text' => '',
            'infotext' => '',
            'streetviewcontrol' => false,
            'directionslink' => false);
    }
    function startEditing(&$view)
    {
        $view->assign('googlemapApiKey', ModUtil::getVar('Content', 'googlemapApiKey'));
        $view->assign('language', ZLanguage::getLanguageCode());
        $view->assign('latitude', $this->latitude);
        $view->assign('longitude', $this->longitude);
        $view->assign('zoom', $this->zoom);
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}

function content_contenttypesapi_googlemap($args)
{
    return new content_contenttypesapi_googlemapPlugin($args['data']);
}

