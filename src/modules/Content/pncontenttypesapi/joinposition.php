<?php
/**
 * Content join position plugin
 *
 * @copyright (C) 2010 Sven Strickroth
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_joinpositionPlugin extends contentTypeBase
{
    var $clear;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'joinposition';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Join Position', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Joins different positions, e.g. if you used position: top-right then this can fix the layout/textflow.', $dom);
    }
    function isTranslatable()
    {
        return false;
    }

    function loadData(&$data)
    {
        if (!isset($data['clear']) || in_array($data['clear'], array('both','left','right')))
            $data['clear'] = 'both';
        $this->clear = $data['clear'];
    }

    function display()
    {
        return '<p style="margin:0;clear:'.$this->clear.';" />';
    }

    function displayEditing()
    {
        return $this->display();
    }

    function getDefaultData()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return array('clear' => 'both');
    }

    function getSearchableText()
    {
        return '';
    }
}

function content_contenttypesapi_joinposition($args)
{
    return new content_contenttypesapi_joinpositionPlugin();
}
