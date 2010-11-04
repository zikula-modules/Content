<?php
/**
* Content YouTube plugin
*
* @copyright (C) 2007-2009, Content Development Team
* @link http://code.zikula.org/content
* @version $Id$
* @license See license.txt
*/


class content_contenttypesapi_VimeoPlugin extends contentTypeBase
{
    var $url;
    var $width;
    var $height;
    var $text;
    var $clipId;
    var $displayMode;

    function getModule()
    { 
        return 'Content';
    }
    function getName()
    {
        return 'vimeo';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Vimeo video clip', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display Vimeo video clip.', $dom);
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
        $this->clipId = $data['clipId'];
        $this->displayMode = isset($data['displayMode']) ? $data['displayMode'] : 'inline';
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('url', $this->url);
        $view->assign('width', $this->width);
        $view->assign('height', $this->height);
        $view->assign('text', $this->text);
        $view->assign('clipId', $this->clipId);
        $view->assign('displayMode', $this->displayMode);

        return $view->fetch('contenttype/vimeo_view.html');
    }
    function displayEditing()
    {
        $output = '<div style="background-color:grey; width:' . $this->width . 'px; height:' . $this->height . 'px; margin:0 auto; padding:10px;">Video-ID : ' . $this->clipId . '</div>';
        $output .= '<p style="width:' . $this->width . 'px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('url' => '',
        'width' => '425',
        'height' => '340',
        'text' => '',
        'clipId' => '',
        'displayMode' => 'inline');
    }
    function isValid(&$data)
    {
        $r = '/vimeo.com\/([-a-zA-Z0-9_]+)/';
        if (preg_match($r, $data['url'], $matches))
        {
            $this->clipId = $data['clipId'] = $matches[1];
            return true;
        }
       
        //$message = $this->__('Error! Unrecognized Vimeo URL', $dom);
        return false;
    }
}

function content_contenttypesapi_Vimeo($args)
{
    return new content_contenttypesapi_VimeoPlugin($args['data']);
}

