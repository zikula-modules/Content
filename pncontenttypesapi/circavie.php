<?php
/**
 * Content YouTube plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_circaviePlugin extends contentTypeBase
{
    var $url;
    var $text;
    var $timelineId;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'circavie';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Circavie timeline', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Embed a timeline from Circavie.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }

    function loadData($data)
    {
        $this->url = $data['url'];
        $this->text = $data['text'];
        $this->timelineId = $data['timelineId'];
    }

    function display()
    {
        $render = pnRender::getInstance('content', false);
        $render->assign('url', $this->url);
        $render->assign('text', $this->text);
        $url = parse_url($this->url);
        $path = explode('/', $url['path']);
        $this->timelineId = $path[2];
        $render->assign('timelineId', $this->timelineId);

        return $render->fetch('contenttype/circavie_view.html');
    }

    function displayEditing()
    {
        $output = '<div style="background-color:grey; height:350px; width:425px; margin:0 auto;"></div>';
        $output .= '<p style="width:425px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }

    function getDefaultData()
    {
        return array('url' => '', 'text' => '', 'timelineId' => '');
    }

}

function content_contenttypesapi_circavie($args)
{
    return new content_contenttypesapi_circaviePlugin($args['data']);
}

