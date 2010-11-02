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

}