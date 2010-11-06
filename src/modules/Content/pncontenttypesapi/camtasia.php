<?php
/**
 * Content camtasia plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_contenttypesapi_camtasiaPlugin extends contentTypeBase
{
    var $text;
    var $width;
    var $height;
    var $videoPath;
    var $displayMode;
	var $folder;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'camtasia';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('camtasia-Flash video', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display camtasia-Flash video.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->text = $data['text'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->videoPath = $data['videoPath'];
        $this->displayMode = isset($data['displayMode']) ? $data['displayMode'] : 'inline';
        $this->folder = $data['folder'];
    }
    function display()
    {
        $render = & pnRender::getInstance('Content', false);
        $render->assign('text', $this->text);
        $render->assign('width', $this->width);
        $render->assign('height', $this->height);
        $render->assign('videoPath', $this->videoPath);
        $render->assign('displayMode', $this->displayMode);
        $render->assign('folder', $this->folder);

        return $render->fetch('contenttype/camtasia_view.html');
    }
    function displayEditing()
    {
        $output = '<div style="background-color:grey; width:320px; height:200px; margin:0 auto; padding:10px;">Flash Video-Path : ' . $this->folder . '/' . $this->videoPath . ',<br />Size in pixels: ' . $this->width . ' x ' . $this->height . '</div>';
        $output .= '<p style="width:320px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('text' => '', 'videoPath' => '', 'displayMode' => 'inline', 'width' => '640', 'height' => '498', 'folder' => 'camtasia');
    }
    function isValid(&$data)
    {
        if (is_file($data['folder'].'/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf')) {
            $this->videoPath = $data['videoPath'];
            return true;
        }
        //$message = $this->__('Unrecognized Flash video path');
        return false;
    }
	function altisValid(&$data, &$message)
	{
	    $videoPath = $data['folder'].'/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf';
	
    	//'camtasia/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf';
	
        if (is_file($data['videoPath'])) {
            $this->videoPath = $data['videoPath'];
            return true;
        }
        $message = __('Unrecognized camtasia-Flash video path: '.$videoPath.'|'.$data['videoPath'], $dom);
        return false;
    }
}

function content_contenttypesapi_camtasia($args)
{
    return new content_contenttypesapi_camtasiaPlugin($args['data']);
}

