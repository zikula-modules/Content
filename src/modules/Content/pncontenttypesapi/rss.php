<?php
/**
 * Content RSS plugin
 *
 * @copyright (C) 2007, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */
class content_contenttypesapi_RSSPlugin extends contentTypeBase
{
    var $url;
    var $includeContent;
    var $refreshTime;
    var $maxNoOfItems;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'rss';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('RSS feed', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display list of items in an RSS feed. Needs the ZFeed system plugin.', $dom);
    }
    function isActive()
    {
        // check for the availability of the ZFeed systemplugin that provides SimplePie
        if (PluginUtil::isAvailable(PluginUtil::getServiceId('SystemPlugin_ZFeed_Plugin'))) {
            return true;
        }
        return false;
    }
    function loadData(&$data)
    {
        $this->url = $data['url'];
        $this->includeContent = $data['includeContent'];
        $this->refreshTime = $data['refreshTime'];
        $this->maxNoOfItems = $data['maxNoOfItems'];
    }
    function display()
    {
        // call ZFeed that provides SimplePie
        $this->feed = new ZFeed($this->url, System::getVar('temp'), $this->refreshTime * 60);
        $items = $this->feed->get_items();
        //$items = $this->feed->get_items(0, $this->maxNoOfItems);
        
        $itemsData = array();
        foreach ($items as $item) {
            if (count($itemsData) < $this->maxNoOfItems) {
                $itemsData[] = array('title' => $this->decode($item->get_title()), 'description' => $this->decode($item->get_description()), 'permalink' => $item->get_permalink());
            }
        }
        $this->feedData = array('title' => $this->decode($this->feed->get_title()), 'description' => $this->decode($this->feed->get_description()), 'permalink' => $this->feed->get_permalink(), 'items' => $itemsData);

        $view = Zikula_View::getInstance('Content', false);
        $view->assign('feed', $this->feedData);
        $view->assign('includeContent', $this->includeContent);

        return $view->fetch('contenttype/rss_view.html');
    }
    function displayEditing()
    {
        return "<input value=\"" . DataUtil::formatForDisplay($this->url) . "\" style=\"width: 30em\" readonly=readonly/>";
    }
    function getDefaultData()
    {
        return array('url' => '', 'includeContent' => false, 'refreshTime' => 60, 'maxNoOfItems' => 10);
    }
    function decode($s)
    {
        return mb_convert_encoding($s, mb_detect_encoding($s), $this->feed->get_encoding());
    }
}

function content_contenttypesapi_RSS($args)
{
    return new content_contenttypesapi_RSSPlugin($args['data']);
}

