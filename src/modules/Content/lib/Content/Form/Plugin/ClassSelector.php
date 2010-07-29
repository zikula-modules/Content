<?php

class Content_Form_Plugin_ClassSelector extends Form_Plugin_DropdownList
{
    function getFilename()
    {
        return __FILE__;
    }

    function load($view, &$params)
    {
        if (!$view->isPostBack()) {
            $classes = ModUtil::apiFunc('Content', 'admin', 'getStyleClasses');
            $empty = array(array('text' => '', 'value' => ''));
            $classes = array_merge($empty, $classes);
            $this->setItems($classes);
        }
        parent::load($view, $params);
    }
}
