<?php
/**
 * Content google map plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_OpenStreetMap extends Content_ContentType
{
    var $longitude;
    var $latitude;
    var $zoom;
    var $text;
    var $height;

    function getTitle()
    {
        return $this->__('OpenStreetMap map');
    }
    function getDescription()
    {
        return $this->__('Display OpenStreetMap map position.');
    }
    function getAdminInfo()
    {
        return $this->__('If you want to add OpenStreetMap maps to your content you don\'t need an API key.');
    }
    function isTranslatable()
    {
        return true;
    }
    function isActive()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->longitude = $data['longitude'];
        $this->latitude = $data['latitude'];
        $this->zoom = $data['zoom'];
        $this->text = $data['text'];
        $this->height = $data['height'];
    }
    function display()
    {
        $scripts = array(
            'javascript/ajax/proto_scriptaculous.combined.min.js',
            'http://www.openlayers.org/api/OpenLayers.js',
            'http://www.openstreetmap.org/openlayers/OpenStreetMap.js',
            'modules/Content/javascript/openstreetmap.js');
        PageUtil::addVar('javascript', $scripts);

        $view = Zikula_View::getInstance('Content', false);
        $view->assign('latitude', $this->latitude);
        $view->assign('longitude', $this->longitude);
        $view->assign('zoom', $this->zoom);
        $view->assign('height', $this->height);
        $view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $view->assign('contentId', $this->contentId);
        $view->assign('language', ZLanguage::getLanguageCode());

        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return DataUtil::formatForDisplay($this->text);
    }
    function getDefaultData()
    {
        return array(
            'latitude' => '52.518611',
            'longitude' => '13.408056',
            'zoom' => 5,
            'text' => '',
            'height' => 300);
    }
    function startEditing(&$view)
    {
        $scripts = array(
            'javascript/ajax/proto_scriptaculous.combined.min.js',
            'http://www.openlayers.org/api/OpenLayers.js',
            'http://www.openstreetmap.org/openlayers/OpenStreetMap.js',
            'modules/Content/javascript/openstreetmap.js');
        PageUtil::addVar('javascript', $scripts);
        
        $view->assign('language', ZLanguage::getLanguageCode());
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}