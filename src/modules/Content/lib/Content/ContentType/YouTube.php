<?php
/**
 * Content YouTube plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_YouTube extends Content_ContentType_Base
{
    var $url;
    var $width;
    var $height;
    var $text;
    var $videoId;
    var $displayMode;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'Youtube';
    }
    function getTitle()
    {
        return $this->__('YouTube video clip');
    }
    function getDescription()
    {
        return $this->__('Display YouTube video clip.');
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->url = $data['url'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->text = $data['text'];
        $this->videoId = $data['videoId'];
        $this->displayMode = isset($data['displayMode']) ? $data['displayMode'] : 'inline';
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('url', $this->url);
        $view->assign('width', $this->width);
        $view->assign('height', $this->height);
        $view->assign('text', $this->text);
        $view->assign('videoId', $this->videoId);
        $view->assign('displayMode', $this->displayMode);

        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $output = '<div style="background-color:grey; width:' . $this->width . 'px; height:' . $this->height . 'px; margin:0 auto; padding:10px;">' . $this->__('Video-ID : %1$s<br />Size in pixels: %2$s x %3$s', $this->videoId, $this->width, $this->height) . ' </div>';
        $output .= '<p style="width:' . $this->width . 'px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('url' => '', 'width' => '320', 'height' => '240', 'text' => '', 'videoId' => '', 'displayMode' => 'inline');
    }
    function isValid(&$data)
    {
        $r = '/\?v=([-a-zA-Z0-9_]+)(&|$)/';
        if (preg_match($r, $data['url'], $matches)) {
            $this->videoId = $data['videoId'] = $matches[1];
            return true;
        }
        return false;
    }
}