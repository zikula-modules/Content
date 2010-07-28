<?php
/**
 * Content Module Display Function plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_ModuleFuncPlugin extends contentTypeBase
{
    var $module;
    var $type;
    var $func;
    var $query;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'modulefunc';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Module display', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display output from any installed module.', $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        // "mf" prefix is to avoid conflict with Zikula's own module/func/type parameters
        $this->module = $data['mfmodule'];
        $this->type = $data['mftype'];
        $this->func = $data['mffunc'];
        $this->query = $data['mfquery'];
    }
    function display()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        static $recursionLevel = 0;
        if ($recursionLevel > 4)
            return __("Maximum number of pages-in-pages reached! You probably included this page in itself.", $dom);

        // Convert "x=5,y=2" to array('x' => 5, 'y' => 2)
        $args = explode(',', $this->query);
        $arguments = array();
        foreach ($args as $arg) {
            if (!empty($arg)) {
                $argument = explode('=', $arg);
                $arguments[$argument[0]] = $argument[1];
            }
        }

        ++$recursionLevel;
        return ModUtil::func($this->module, $this->type, $this->func, $arguments);
        --$recursionLevel;
    }
    function displayEditing()
    {
        $output = "module=$this->module, type=$this->type, func=$this->func, query=$this->query";
        return $output;
    }
    function getDefaultData()
    {
        return array('mfmodule' => '', 'mftype' => 'user', 'mffunc' => 'view', 'mfquery' => '');
    }
}

function content_contenttypesapi_ModuleFunc($args)
{
    return new content_contenttypesapi_ModuleFuncPlugin($args['data']);
}

