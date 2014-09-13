<?php

/**
 * Search plugin info
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */
class Content_Api_Search extends Zikula_AbstractApi
{
    /**
     * Search plugin info
     */
    public function info()
    {
        return array(
            'title' => 'Content', 
            'functions' => array('Content' => 'search')
        );
    }

    /**
     * Renders available search form options.
     */
    public function options($args)
    {
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_READ)) {
            $render = Zikula_View::getInstance('Content');
            $render->assign('active', (isset($args['active']) && isset($args['active']['Content'])) || (!isset($args['active'])));

            return $render->fetch('search/options.tpl');
        }

        return '';
    }

    /**
     * Performs the actual search processing.
     */
    public function search($args)
    {
        ModUtil::dbInfoLoad('Search');
        $dbtables = DBUtil::getTables();

        $searchTable = $dbtables['search_result'];
        $searchColumn = $dbtables['search_result_column'];

        $pageTable = $dbtables['content_page'];
        $pageColumn = $dbtables['content_page_column'];
        $contentTable = $dbtables['content_content'];
        $contentColumn = $dbtables['content_content_column'];
        $contentSearchTable = $dbtables['content_searchable'];
        $contentSearchColumn = $dbtables['content_searchable_column'];
        $translatedPageTable = $dbtables['content_translatedpage'];
        $translatedPageColumn = $dbtables['content_translatedpage_column'];

        $sessionId = session_id();

        $multilingual = System::getVar('multilingual');
        $defaultLanguage = System::getVar('language_i18n');
        $currentLanguage = ZLanguage::getLanguageCode();

        // check whether we need to search also in translated content
        $searchInTranslations = ($multilingual && $currentLanguage != $defaultLanguage);

        $searchWhereClauses = array();
        $searchWhereClauses[] = Search_Api_User::construct_where($args, array($pageColumn['title']), $pageColumn['language']);
        if ($searchInTranslations) {
            $searchWhereClauses[] = Search_Api_User::construct_where($args, array($translatedPageColumn['title']), $translatedPageColumn['language']);
        }
        $searchWhereClauses[] = Search_Api_User::construct_where($args, array($contentSearchColumn['text']), $contentSearchColumn['language']);

        // add default filters
        $whereClauses = array();
        $whereClauses[] = '(' . implode(' OR ', $searchWhereClauses) . ')';
        $whereClauses[] = $pageColumn['active'] . ' = 1';
        $whereClauses[] = "($pageColumn[activeFrom] IS NULL OR $pageColumn[activeFrom] <= NOW())";
        $whereClauses[] = "($pageColumn[activeTo] IS NULL OR $pageColumn[activeTo] >= NOW())";
        $whereClauses[] = $contentColumn['active'] . ' = 1';
        $whereClauses[] = $contentColumn['visiblefor'] . (UserUtil::isLoggedIn() ? ' <= 1' : ' >= 1');

        // if searching in non-default languages, we need the translated title
        $titleField = $searchInTranslations ? $translatedPageColumn['title'] : $pageColumn['title'];

        // join also the translation table if required
        $additionalJoins = '';
        if ($searchInTranslations) {
            $additionalJoins = "JOIN $translatedPageTable ON $translatedPageColumn[pageId] = $pageColumn[id] AND $translatedPageColumn[language] = '$currentLanguage'";
            $whereClauses[] = $contentSearchColumn['language'] . ' = \'' . $currentLanguage . '\'';
        }

        $where = implode(' AND ', $whereClauses);

        $selection = "
            SELECT DISTINCT $titleField,
            $contentSearchColumn[text],
            'Content',
            $pageColumn[id],
            $pageColumn[cr_date] AS createdDate,
            '" . DataUtil::formatForStore($sessionId) . "'
            FROM $pageTable
            JOIN $contentTable
            ON $contentColumn[pageId] = $pageColumn[id]
            JOIN $contentSearchTable
            ON $contentSearchColumn[contentId] = $contentColumn[id]
            $additionalJoins
            WHERE $where
        ";

        // Direct SQL way of searching in titles and searchable content items
        // for Pages and Content items that are visible/active
        $sql = "INSERT INTO $searchTable
            ($searchColumn[title],
            $searchColumn[text],
            $searchColumn[module],
            $searchColumn[extra],
            $searchColumn[created],
            $searchColumn[session])
            $selection
        ";

        $dbresult = DBUtil::executeSQL($sql);
        if (!$dbresult) {
            return LogUtil::registerError($this->__('Error! Could not load any Content pages or items.'));
        }

        return true;
    }

    /**
     * Do last minute access checking and assign URL to items
     *
     * Access checking is ignored since access check has
     * already been done. But we do add a URL to the found user
     */
    public function search_check($args)
    {
        $datarow = &$args['datarow'];
        $pageId = $datarow['extra'];
        $datarow['url'] = ModUtil::url('Content', 'user', 'view', array('pid' => $pageId));

        return true;
    }
}
