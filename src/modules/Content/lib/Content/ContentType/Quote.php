<?php
/**
 * Content quote plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Quote extends Content_ContentType
{
    var $text;
    var $inputType;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'Quote';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Quote', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('A highlighted quote with source.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->text = $data['text'];
        $this->source = $data['source'];
        $this->desc = $data['desc'];
    }
    function display()
    {
        $text = DataUtil::formatForDisplayHTML($this->text);
        $source = DataUtil::formatForDisplayHTML($this->source);
        $desc = DataUtil::formatForDisplayHTML($this->desc);

        $view = Zikula_View::getInstance('Content');
        $event = new Zikula_Event('content.hook.contentitem.ui.filter', $view, array('caller' => $this->getModule()), $text);
        $text = $view->getEventManager()->notify($event)->getData();
        $view->assign('source', $source);
        $view->assign('text', $text);
        $view->assign('desc', $desc);

        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $text = DataUtil::formatForDisplayHTML($this->text);
        $source = DataUtil::formatForDisplayHTML($this->source);
        $desc = DataUtil::formatForDisplayHTML($this->desc);

//        $text = ModUtil::callHooks('item', 'transform', '', array($text));
//        $text = trim($text[0]);
        $view = Zikula_View::getInstance('Content');
        $event = new Zikula_Event('content.hook.contentitem.ui.filter', $view, array('caller' => $this->getModule()), $text);
        $text = $view->getEventManager()->notify($event)->getData();

        $text = '<div class="content-quote"><blockquote>' . $text . '</blockquote><p>-- ' . $desc . '</p></div>';

        return $text;
    }
    function getDefaultData()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return array('text' => __('Add quote text here...', $dom), 'source' => 'http://', 'desc' => __('Name of the Source', $dom));
    }
    function startEditing(&$view)
    {
        $scripts = array('javascript/ajax/prototype.js', 'javascript/helpers/Zikula.js');
        PageUtil::addVar('javascript', $scripts);
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}