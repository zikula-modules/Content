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
        $ok = ModUtil::apiFunc('Content', 'Content', 'dragContent', array('pageId' => FormUtil::getPassedValue('pid', null, 'POST'), 
                'contentId' => FormUtil::getPassedValue('cid', null, 'P'), 
                'contentAreaIndex' => FormUtil::getPassedValue('cai', null, 'P'),
                'position' => FormUtil::getPassedValue('pos', null, 'P')));
        if (!$ok) {
            return new Zikula_Response_Ajax(array('ok' => false, 'message' => LogUtil::getErrorMessagesText()));
        }
        return new Zikula_Response_Ajax(array('ok' => true, 'message' => $this->__('OK')));
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
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());
        
        $id = (int)FormUtil::getPassedValue('id', -1, 'POST');
        $active = FormUtil::getPassedValue('active', null, 'POST');
        if ($id == -1) {
            AjaxUtil::error(LogUtil::registerError($this->__('Error! No page ID passed.')));
        }

        $ok = ModUtil::apiFunc('Content', 'Page', 'updateState', array('pageId' => $id, 'active' => $active));
        if (!$ok) {      
            AjaxUtil::error(LogUtil::registerError($this->__('Error! Could not update state.')));
        }
        return new Zikula_Response_Ajax(array('id' => $id));
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
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT), LogUtil::getErrorMsgPermission());
        
        $id = (int)FormUtil::getPassedValue('id', -1, 'POST');
        $inMenu = FormUtil::getPassedValue('inMenu', null, 'POST');
        if ($id == -1) {
            AjaxUtil::error(LogUtil::registerError($this->__('Error! No page ID passed.')));
        }
        
        $ok = ModUtil::apiFunc('Content', 'Page', 'updateState', array('pageId' => $id, 'inMenu' => $inMenu));
        if (!$ok) {
            AjaxUtil::error(LogUtil::registerError($this->__('Error! Could not update state.')));
        }
        return new Zikula_Response_Ajax(array('id' => $id));
    }
}