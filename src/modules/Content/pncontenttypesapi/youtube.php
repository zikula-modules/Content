<?php
/**
 * Content YouTube plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_YouTubePlugin extends contentTypeBase
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
        return 'youtube';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('YouTube video clip', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display YouTube video clip.', $dom);
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

        return $view->fetch('contenttype/youtube_view.html');
    }
    function displayEditing()
    {
        $output = '<div style="background-color:grey; width:' . $this->width . 'px; height:' . $this->height . 'px; margin:0 auto; padding:10px;">Video-ID : ' . $this->videoId . ',<br />Size in pixels: ' . $this->width . ' x ' . $this->height . ' </div>';
        $output .= '<p style="width:' . $this->width . 'px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('url' => '', 'width' => '320', 'height' => '240', 'text' => '', 'videoId' => '', 'displayMode' => 'inline');
    }
    function isValid(&$data, &$message)
    {
        $r = '/\?v=([-a-zA-Z0-9_]+)(&|$)/';
        if (preg_match($r, $data['url'], $matches)) {
            $this->videoId = $data['videoId'] = $matches[1];
            return true;
        }
        $message = __('Unrecognized YouTube URL', $dom);
        return false;
    }
}

function content_contenttypesapi_YouTube($args)
{
    return new content_contenttypesapi_YouTubePlugin($args['data']);
}

