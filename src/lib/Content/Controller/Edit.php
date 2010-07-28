<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnedit.php 406 2010-06-01 03:04:45Z drak $
 * @license See license.txt
 */


class Content_Controller_Edit extends Zikula_Controller
{
    /*=[ Main page tree ]============================================================*/

    public function main($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_main.html', new Content_Form_Handler_Edit_Main($args));
    }

    /*=[ Create new page ]===========================================================*/

    public function newpage($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_newpage.html', new Content_Form_Handler_Edit_NewPage($args));
    }

    /*=[ Edit single page ]==========================================================*/

    public function editpage($args)
    {
        $render = FormUtil::newpnForm('Content'); // get all config vars and assign them to the template
        return $render->pnFormExecute('content_edit_editpage.html', new Content_Form_Handler_Edit_Page($args));
    }

    /*=[ New content element ]=======================================================*/

    public function newcontent($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_newcontent.html', new Content_Form_Handler_Edit_NewContent($args));
        //echo $render->pnFormExecute('content_blankpage.html', new content_edit_newContentHandler($args));
//return true;
    }

    /*=[ Edit single content item ]==================================================*/

    public function editcontent($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_editcontent.html', new Content_Form_Handler_Edit_EditContent($args));
    }

    /*=[ Translate page ]============================================================*/


    public function translatepage($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_translatepage.html', new Content_Form_Handler_Edit_TranslatePage($args));
    }


    /*=[ Translate content item ]====================================================*/


    public function translatecontent($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_translatecontent.html', new Content_Form_Handler_Edit_TtranslateContent($args));
    }

    /*=[ History ]===================================================================*/


    public function history($args)
    {
        $render = FormUtil::newpnForm('Content');
        return $render->pnFormExecute('content_edit_history.html', new Content_Form_Handler_Edit_HistoryContent($args));
    }
}