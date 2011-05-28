<?php
/**
 * Content unfiltered text plugin
 *
 * @copyright (C) 2007-2011, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_contenttypesapi_unfilteredPlugin extends contentTypeBase
{
    var $text;
    var $useiframe;
    var $iframetitle;
    var $iframename;
    var $iframesrc;
    var $iframestyle;
    var $iframewidth;
    var $iframeheight;
    var $iframeborder;
    var $iframescrolling;
    var $iframeallowtransparancy;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'unfiltered';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Unfiltered raw text', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('A plugin for unfiltered raw output (iframes, JavaScript, banners, etc)', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function isActive()
    {
        // Only active when the admin has enabled this plugin
        if (pnModGetVar('content', 'enableRawPlugin')) {
            return true;
        } else {
            return false;
        }
    }
    function loadData(&$data)
    {
        $this->text = $data['text'];
        $this->useiframe = $data['useiframe'];
        $this->iframetitle = $data['iframetitle'];
        $this->iframename = $data['iframename'];
        $this->iframesrc = $data['iframesrc'];
        $this->iframestyle = $data['iframestyle'];
        $this->iframewidth = $data['iframewidth'];
        $this->iframeheight = $data['iframeheight'];
        $this->iframeborder = $data['iframeborder'];
        $this->iframescrolling = $data['iframescrolling'];
        $this->iframeallowtransparancy = $data['iframeallowtransparancy'];
    }

    function display()
    {
        $render = & pnRender::getInstance('content', false);
        $render->assign('text', $this->text);
        $render->assign('useiframe', $this->useiframe);
        $render->assign('iframetitle', $this->iframetitle);
        $render->assign('iframename', $this->iframename);
        $render->assign('iframesrc', $this->iframesrc);
        $render->assign('iframestyle', $this->iframestyle);
        $render->assign('iframewidth', $this->iframewidth);
        $render->assign('iframeheight', $this->iframeheight);
        $render->assign('iframeborder', $this->iframeborder);
        $render->assign('iframescrolling', $this->iframescrolling);
        $render->assign('iframeallowtransparancy', $this->iframeallowtransparancy);
        return $render->fetch('contenttype/unfiltered_view.html');
    }

    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('content');
        if ($this->useiframe) {
            $output = '<div style="background-color:Lavender; padding:10px;">' . __f('An <strong>iframe</strong> is included with<br />src = %1$s<br />width = %2$s and height = %3$s', array($this->iframesrc, $this->iframewidth, $this->iframeheight), $dom) . '</div>';
        } else {
            $output = '<div style="background-color:Lavender; padding:10px;">' . __f('The following <strong>unfiltered text</strong> will be included literally<br /><hr>%s<hr>', DataUtil::formatForDisplay($this->text), $dom) . '</div>';
        }
        return $output;
    }

    function getDefaultData()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return array(
            'text' => __('Add unfiltered text here ...', $dom),
            'useiframe' => false,
            'iframetitle' => '',
            'iframename' => '',
            'iframesrc' => '',
            'iframestyle' => 'border:0',
            'iframewidth' => '800',
            'iframeheight' => '600',
            'iframeborder' => '0',
            'iframescrolling' => 'no',
            'iframeallowtransparancy' => true
            );
    }
}

function content_contenttypesapi_unfiltered($args)
{
    return new content_contenttypesapi_unfilteredPlugin();
}
