<?php
/**
 * Search plugin info
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */
function content_searchapi_info()
{
    return array('title' => 'content', 'functions' => array('content' => 'search'));
}

/**
 * Search form component
 **/
function content_searchapi_options($args)
{
    if (SecurityUtil::checkPermission('content::', '::', ACCESS_READ)) {
        $render = & pnRender::getInstance('content');
        $render->assign('active', (isset($args['active']) && isset($args['active']['content'])) || (!isset($args['active'])));
        return $render->fetch('content_search_options.html');
    }

    return '';
}

/**
 * Search plugin main function
 **/
function content_searchapi_search($args)
{
    $dom = ZLanguage::getModuleDomain('content');
    pnModDBInfoLoad('content');
    pnModDBInfoLoad('Search');
    $dbconn = pnDBGetConn(true);
    $pntable = pnDBGetTables();

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
    $wheretitle = search_construct_where($args, array($pageColumn['title']), null);

    $sql = "INSERT INTO $searchTable
  ($searchColumn[title],
   $searchColumn[text],
   $searchColumn[module],
   $searchColumn[extra],
   $searchColumn[created],
   $searchColumn[session])
SELECT distinct $pageColumn[title],
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
WHERE ($where or $wheretitle) and $pageColumn[active] = 1 and $contentColumn[active] = 1 and $contentColumn[visiblefor] ".(pnUserLoggedIn()?'<=1':'>=1');

    $dbresult = DBUtil::executeSQL($sql);
    if (!$dbresult)
        return LogUtil::registerError(__('Error! Could not load items.', $dom));

    return true;
}

/**
 * Do last minute access checking if needed and assign URL to items
 **/
function content_searchapi_search_check(&$args)
{
    $datarow = &$args['datarow'];
    $pageId = (int) $datarow['extra'];

    $datarow['url'] = pnModUrl('content', 'user', 'view', array('pid' => $pageId));

    return true;
}
