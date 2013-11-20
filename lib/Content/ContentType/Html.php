<?php

/**
 * Content html plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */
class Content_ContentType_Html extends Content_AbstractContentType
{
    protected $text;
    protected $inputType;

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getInputType()
    {
        return $this->inputType;
    }

    public function setInputType($inputType)
    {
        $this->inputType = $inputType;
    }

    function getTitle()
    {
        return $this->__('HTML text');
    }

    function getDescription()
    {
        return $this->__('A HTML editor for adding text to your page.');
    }

    function isTranslatable()
    {
        return true;
    }

    function loadData(&$data)
    {
        if (!isset($data['inputType'])) {
            $data['inputType'] = 'html';
        }
        $this->text = $data['text'];
        $this->inputType = $data['inputType'];
    }

    function display()
    {
        if ($this->inputType == 'raw') {
            $text = DataUtil::formatForDisplay($this->text);
        } else {
            $text = DataUtil::formatForDisplayHTML($this->text);
        }

        $this->view->assign('inputType', $this->inputType);
        $this->view->assign('text', $text);

        return $this->view->fetch($this->getTemplate());
    }

    function displayEditing()
    {
        return $this->display();
    }

    function getDefaultData()
    {
        return array(
            'text' => $this->__('Add text here ...'),
            'inputType' => 'html');
    }

    function startEditing()
    {
        PageUtil::addVar('javascript', array('javascript/ajax/prototype.js', 'javascript/helpers/Zikula.js'));
        if (isset($this->inputType)) {
            $this->view->assign('pluginInputType', $this->inputType);
        }
        $this->view->assign('cid', $this->contentId);
    }

    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }

    public function getTemplate()
    {
        $this->view->setCacheId($this->contentId);
        return 'contenttype/html_view.tpl';
    }

	/* Override method for simple template inclusion */
    public function getEditTemplate()
    {
        return 'contenttype/html_edit.tpl';
    }
	

}