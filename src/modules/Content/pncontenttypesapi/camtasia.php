<?php
/**
 * Content camtasia plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: mytube.php 375 2010-01-06 13:34:29Z herr.vorragend $
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
    var $author;
    var $thumbwidth;
    var $thumbheight;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'camtasia';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Camtasia Flash video', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Display a Camtasia Flash video.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData($data)
    {
        $this->text = $data['text'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->videoPath = $data['videoPath'];
        $this->displayMode = isset($data['displayMode']) ? $data['displayMode'] : 'inline';
        $this->folder = $data['folder'];
        $this->author = $data['author'];
        $this->thumbwidth = $data['thumbwidth'];
        $this->thumbheight = $data['thumbheight'];
    }
    function display()
    {
        $render = & pnRender::getInstance('content', false);
        $render->assign('text', $this->text);
        $render->assign('width', $this->width);
        $render->assign('height', $this->height);
        $render->assign('videoPath', $this->videoPath);
        $render->assign('displayMode', $this->displayMode);
        $render->assign('folder', $this->folder);
        $render->assign('author', $this->author);
        $render->assign('thumbwidth', $this->thumbwidth);
        $render->assign('thumbheight', $this->thumbheight);

        return $render->fetch('contenttype/camtasia_view.html');
    }
    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $output = '<div style="background-color:Lavender; width:320px; height:200px; margin:0 auto; padding:10px;">'.__f('Flash Video-Path: %1$s<br>Size in pixels: %2$s', array($this->folder.'/'.$this->videoPath, $this->width.'x'.$this->height), $dom) . '<img width="300" height="140" src="'.$this->folder.'/'.$this->videoPath.'/FirstFrame.png" alt="" /></div>';
        $output .= '<p style="width:320px; margin:0 auto;">' . __f('Video description: %s', DataUtil::formatForDisplay($this->text), $dom) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array(
            'text' => '', 
            'videoPath' => '', 
            'displayMode' => 'inline', 
            'width' => '640', 
            'height' => '498', 
            'folder' => 'camtasia',
            'author' => '',
            'thumbwidth' => '48',
            'thumbheight' => '48');
    }
    function isValid(&$data, &$message)
    {
        $dom = ZLanguage::getModuleDomain('content');
        if (is_file($data['folder'].'/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf')) {
            $this->videoPath = $data['videoPath'];
            return true;
        }
        $message = __f('Unrecognized Flash video path: %s', $data['videoPath'], $dom);
        return false;
    }
	function altisValid(&$data, &$message)
	{
	    $dom = ZLanguage::getModuleDomain('content');
	    $videoPath = $data['folder'].'/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf';
	
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

