<?php

/**
 * Content camtasia plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
class Content_ContentType_Camtasia extends Content_AbstractContentType
{
    protected $text;
    protected $width;
    protected $height;
    protected $videoPath;
    protected $displayMode;
    protected $folder;

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getVideoPath()
    {
        return $this->videoPath;
    }

    public function setVideoPath($videoPath)
    {
        $this->videoPath = $videoPath;
    }

    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    function getTitle()
    {
        return $this->__('camtasia-Flash video');
    }

    function getDescription()
    {
        return $this->__('Display camtasia-Flash video.');
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
        $this->view->assign('text', $this->text);
        $this->view->assign('width', $this->width);
        $this->view->assign('height', $this->height);
        $this->view->assign('videoPath', $this->videoPath);
        $this->view->assign('displayMode', $this->displayMode);
        $this->view->assign('folder', $this->folder);

        return $this->view->fetch($this->getTemplate());
    }

    function displayEditing()
    {
        $output = '<div style="background-color:grey; width:320px; height:200px; margin:0 auto; padding:10px;">Flash Video-Path : ' . $this->folder . '/' . $this->videoPath . ',<br />Size in pixels: ' . $this->width . ' x ' . $this->height . '</div>';
        $output .= '<p style="width:320px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }

    function getDefaultData()
    {
        return array('text' => '',
            'videoPath' => '',
            'displayMode' => 'inline',
            'width' => '640',
            'height' => '498',
            'folder' => 'camtasia');
    }

    function isValid(&$data)
    {
        if (is_file($data['folder'] . '/' . $data['videoPath'] . '/' . $data['videoPath'] . '_controller.swf')) {
            $this->videoPath = $data['videoPath'];
            return true;
        }
        return false;
    }

    function altisValid(&$data)
    {
        $videoPath = $data['folder'] . '/' . $data['videoPath'] . '/' . $data['videoPath'] . '_controller.swf';
        //'camtasia/'.$data['videoPath'].'/'.$data['videoPath'].'_controller.swf';

        if (is_file($data['videoPath'])) {
            $this->videoPath = $data['videoPath'];
            return true;
        }
        return false;
    }

}