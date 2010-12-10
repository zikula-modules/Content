<?php
/**
 * Content join position plugin
 *
 * @copyright (C) 2010 Sven Strickroth
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_contenttypesapi_joinpositionPlugin extends contentTypeBase
{
    var $clear;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'joinposition';
    }
    function getTitle()
    {
        return $this->__('Join Position');
    }
    function getDescription()
    {
        return $this->__('Joins different positions, e.g. if you used position: top-right then this can fix the layout/textflow.');
    }
    function isTranslatable()
    {
        return false;
    }

    function loadData(&$data)
    {
        if (!isset($data['clear']) || in_array($data['clear'], array('both','left','right'))) {
            $data['clear'] = 'both';
		}
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
