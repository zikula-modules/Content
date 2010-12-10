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
     * This function toggles active/inactive
     *
     * @author Erik Spaan & Sven Strickroth
     * @param id int  id of page to toggle
     * @param active  string "true"/"false"
     * @return mixed true or Ajax error
     */
    public function togglepagestate($args)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            AjaxUtil::error($this->__('Sorry! You have not been granted access to this page.'));
        }
        
        $id = FormUtil::getPassedValue('id', -1, 'GET');
        if ($id == -1) {
            return AjaxUtil::error(LogUtil::registerError($this->__('Error! No page ID passed.')));
        }
        
        $ok = ModUtil::apiFunc('Content', 'page', 'updateState', array('pageId' => $id, 'active' => ((bool)FormUtil::getPassedValue('active', 'false', 'GET'))));
        if (!$ok) {
            return AjaxUtil::error(LogUtil::registerError($this->__('Error! Could not update state.')));
        }
        AjaxUtil::output(array('id' => $id));
    }
    
    /**
     * togglepageinmenu
     * This function toggles inmenu/outmenu
     *
     * @author Erik Spaan & Sven Strickroth
     * @param id int  id of page to toggle
     * @param inmenu  string "true"/"false"
     * @return mixed true or Ajax error
     */
    public function togglepageinmenu($args)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            AjaxUtil::error($this->__('Sorry! You have not been granted access to this page.'));
        }
        
        $id = FormUtil::getPassedValue('id', -1, 'GET');
        if ($id == -1) {
            return AjaxUtil::error(LogUtil::registerError($this->__('Error! No page ID passed.')));
        }
        
        $ok = ModUtil::apiFunc('Content', 'page', 'updateState', array('pageId' => $id, 'inMenu' => ((bool)FormUtil::getPassedValue('inmenu', 'false', 'GET'))));
        if (!$ok) {
            return AjaxUtil::error(LogUtil::registerError($this->__('Error! Could not update state.')));
        }
        AjaxUtil::output(array('id' => $id));
    }
}