<?php
/**
 * Content google map plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: googlemap.php 356 2010-01-04 14:43:31Z herr.vorragend $
 * @license See license.txt
 */

class content_contenttypesapi_openstreetmapPlugin extends contentTypeBase
{
    var $longitude;
    var $latitude;
    var $zoom;
    var $text;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'openstreetmap';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('OpenStreetMap map', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Display OpenStreetMap map position.', $dom);
    }
    function getAdminInfo()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('If you want to add OpenStreetMap maps to your content you don\'t need a key.', $dom);
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
        $this->text = $data['text'];
    }

    function display()
    {
        $scripts = array('javascript/ajax/prototype.js', 'http://www.openlayers.org/api/OpenLayers.js', 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js', 'modules/content/pnjavascript/openstreetmap.js');
        PageUtil::addVar('javascript', $scripts);

        $render = & pnRender::getInstance('content', false);
        $render->assign('longitude', $this->longitude);
        $render->assign('latitude', $this->latitude);
        $render->assign('zoom', $this->zoom);
        $render->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $render->assign('contentId', $this->contentId);

        return $render->fetch('contenttype/openstreetmap_view.html');
    }

    function displayEditing()
    {
        return DataUtil::formatForDisplay($this->text);
    }

    function getDefaultData()
    {
        return array('latitude' => '52.518611', 'longitude' => '13.408056', 'zoom' => 5, 'text' => '');
    }

    function startEditing(&$render)
    {
        $scripts = array('javascript/ajax/prototype.js', 'http://www.openlayers.org/api/OpenLayers.js', 'http://www.openstreetmap.org/openlayers/OpenStreetMap.js', 'modules/content/pnjavascript/openstreetmap.js');
        PageUtil::addVar('javascript', $scripts);
    }

    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}

function content_contenttypesapi_openstreetmap($args)
{
    return new content_contenttypesapi_openstreetmapPlugin($args['data']);
}

