<?php
/**
 * Search plugin info
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnsearchapi.php 403 2010-05-31 18:15:19Z drak $
 * @license See license.txt
 */


class Content_Api_Search extends Zikula_Api
{
    public function info()
    {
        return array('title' => 'content', 'functions' => array('Content' => 'search'));
    }

    /**
     * Search form component
     **/
    public function options($args)
    {
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_READ)) {
            $view = Zikula_View::getInstance('Content');
            $view->assign('active', (isset($args['active']) && isset($args['active']['content'])) || (!isset($args['active'])));
            return $view->fetch('content_search_options.html');
        }

        return '';
    }

    public function search($args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        ModUtil::dbInfoLoad('Content');
        ModUtil::dbInfoLoad('Search');
        $dbconn = DBConnectionStack::getConnection*(true);
        $pntable = DBUtil::getTables();

        $searchTable = $pntable['search_result'];
        $searchColumn = $pntable['search_result_column'];
        $pageTable = $pntable['content_page'];
        $pageColumn = $pntable['content_page_column'];
        $contentTable = $pntable['content_content'];
        $contentColumn = $pntable['content_content_column'];
        $contentSearchTable = $pntable['content_searchable'];
        $contentSearchColumn = $pntable['content_searchable_column'];

        $sessionId = session_id();

        $where = search_construct_where($args, array($contentSearchColumn['text']), null);

        $sql = "INSERT INTO $searchTable
  ($searchColumn[title],
                $searchColumn[text],
                $searchColumn[module],
                $searchColumn[extra],
                $searchColumn[created],
                $searchColumn[session])
SELECT $pageColumn[title],
                $contentSearchColumn[text],
       'content',
                $pageColumn[id],
                $pageColumn[cr_date] AS createdDate,
       '" . DataUtil::formatForStore($sessionId) . "'
FROM $pageTable
JOIN $contentTable
     ON $contentColumn[pageId] = $pageColumn[id]
JOIN $contentSearchTable
     ON $contentSearchColumn[contentId] = $contentColumn[id]
WHERE $where";

        $dbresult = DBUtil::executeSQL($sql);
        if (!$dbresult)
            return LogUtil::registerError(__('Error! Could not load items.', $dom));

        return true;
    }

    public function search_check(&$args)
    {
        $datarow = &$args['datarow'];
        $pageId = (int) $datarow['extra'];

        $datarow['url'] = ModUtil::url('Content', 'user', 'view', array('pid' => $pageId));

        return true;
    }
}