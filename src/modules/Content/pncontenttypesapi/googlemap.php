<?php
/**
 * Content google map plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: googlemap.php 356 2010-01-04 14:43:31Z herr.vorragend $
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
        return 'content';
    }
    function getName()
    {
        return 'googlemap';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Google map', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Display Google map position.', $dom);
    }
    function getAdminInfo()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('A Google maps API key is not needed any more in the new version 3 of the Javascript API.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function isActive()
    {
        return true;
    }
    function loadData($data)
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
        $render = & pnRender::getInstance('content', false);
        $render->assign('longitude', $this->longitude);
        $render->assign('latitude', $this->latitude);
        $render->assign('zoom', $this->zoom);
        $render->assign('height', $this->height);
        $render->assign('text', $this->text);
        $render->assign('infotext', $this->infotext);
        $render->assign('streetviewcontrol', $this->streetviewcontrol);
        $render->assign('directionslink', $this->directionslink);
        $render->assign('contentId', $this->contentId);
        $render->assign('language', ZLanguage::getLanguageCode());

        // Load the Google Maps JS api v3
        PageUtil::setVar('javascript', 'http://maps.google.com/maps/api/js?v=3&language=' . ZLanguage::getLanguageCode() . '&sensor=false');

        return $render->fetch('contenttype/googlemap_view.html');
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
    function startEditing(&$render)
    {
        $render->assign('language', ZLanguage::getLanguageCode());
        $render->assign('longitude', $this->longitude);
        $render->assign('latitude', $this->latitude);
        $render->assign('zoom', $this->zoom);
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

