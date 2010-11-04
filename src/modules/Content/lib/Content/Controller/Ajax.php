<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */


class Content_Controller_Ajax extends Zikula_Controller
{
    public function dragcontent($args)
    {
        $ok = ModUtil::apiFunc('Content', 'Content', 'dragcontent', array('pageId' => FormUtil::getPassedValue('pid', null, 'P'), 
                'contentId' => FormUtil::getPassedValue('cid', null, 'P'), 
                'contentAreaIndex' => FormUtil::getPassedValue('cai', null, 'P'),
                'position' => FormUtil::getPassedValue('pos', null, 'P')));
        if (!$ok) {
            return array('ok' => false, 'message' => LogUtil::getErrorMessagesText());
        }

        return array('ok' => true, 'message' => $this->__('OK'));
    }

    /**
     * togglepagestate
     * This function toggles online/offline
     *
     * @author Erik Spaan
     * @param id int  id of page to toggle
     * @return mixed true or Ajax error
     */
    public function togglepagestate($args)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            AjaxUtil::error($this->__('Sorry! You have not been granted access to this page.'));
        }
        
        $id = FormUtil::getPassedValue('id', -1, 'GET');
        if ($id == -1) {
            return AjaxUtil::error(LogUtil::registerError($this->__('No page ID passed.')));
        }
    
        // read the page information
        $pageData = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $id, 'filter' => array('checkActive' => false), 'enableEscape' => false, 'includeContent' => false, 'includeLanguages' => false));
        if ($pageData === false) {
            return AjaxUtil::error(LogUtil::registerError($this->__f('Error! Could not retrieve page with ID %s.', DataUtil::formatForDisplay($id))));
        }
        // toggle the active state
        if ($pageData['active'] == 1) {
            $active = 0;
        } else {
            $active = 1;
        }
    
        $ok = ModUtil::apiFunc('Content', 'Page', 'updateState', array('pageId' => $id, 'active' => $active, 'inMenu' => $pageData['inMenu']));
        if (!$ok) {
            return AjaxUtil::error(LogUtil::registerError($this->__('Error! Could not update state.')));
        }
        AjaxUtil::output(array('id' => $id));
    }
    
    /**
     * togglepageinmenu
     * This function toggles inmenu/outmenu
     *
     * @author Erik Spaan
     * @param id int  id of page to toggle
     * @return mixed true or Ajax error
     */
    public function togglepageinmenu($args)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            AjaxUtil::error($this->__('Sorry! You have not been granted access to this page.'));
        }
        
        $id = FormUtil::getPassedValue('id', -1, 'GET');
        if ($id == -1) {
            LogUtil::registerError($this->__('No page ID passed.'));
            AjaxUtil::output();
        }
    
        // read the page information
        $pageData = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $id, 'filter' => array('checkActive' => false), 'enableEscape' => false, 'includeContent' => false, 'includeLanguages' => false));
        if ($pageData === false) {
            LogUtil::registerError($this->__f('Error! Could not retrieve page with ID %s.', DataUtil::formatForDisplay($id)));
            AjaxUtil::output();
        }
        // toggle the inMenu state
        if ($pageData['inMenu'] == 1) {
            $inMenu = 0;
        } else {
            $inMenu = 1;
        }
    
        $ok = ModUtil::apiFunc('Content', 'Page', 'updateState', array('pageId' => $id, 'active' => $pageData['active'], 'inMenu' => $inMenu));
        if (!$ok) {
            LogUtil::registerError($this->__('Error! Could not update state.'));
            AjaxUtil::output();
        }
        return array('id' => $id);
    }
}