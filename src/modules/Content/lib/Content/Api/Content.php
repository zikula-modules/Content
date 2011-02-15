<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

require_once 'modules/Content/common.php';
include_once 'modules/Content/includes/contentTypeBase.php';

class Content_Api_Content extends Zikula_Api
{

    /*=[ Standard CRUD methods ]=====================================================*/

    public function getContent($args)
    {

        $id = (int) $args['id'];
        $language = (array_key_exists('language', $args) ? $args['language'] : ZLanguage::getLanguageCode());
        $translate = (array_key_exists('translate', $args) ? $args['translate'] : true);

        $content = $this->contentGetContent('content', $id, true, $language, $translate);
        if ($content === false)
            return false;
        if (count($content) == 0)
            return LogUtil::registerError($this->__("Error! Unknown content-ID"));

        return $content[0];
    }

    public function getPageContent($args)
    {
        $pageId = (int) $args['pageId'];
        $editing = (array_key_exists('editing', $args) ? $args['editing'] : false);
        $language = (array_key_exists('language', $args) ? $args['language'] : ZLanguage::getLanguageCode());
        $translate = (array_key_exists('translate', $args) ? $args['translate'] : true);

        $contentList = $this->contentGetContent('page', $pageId, $editing, $language, $translate);

        $content = array();
        foreach ($contentList as $c) {
            $c['title'] = $c['plugin']->getTitle();
            $c['isTranslatable'] = $c['plugin']->isTranslatable();
            $output = $c['plugin']->displayStart();
            if ($editing) {
                $output .= $c['plugin']->displayEditing();
            } else {
                $output .= $c['plugin']->display();
            }
            $output .= $c['plugin']->displayEnd();
            $c['output'] = $output;
            $content[$c['areaIndex']][] = $c;
        }

        return $content;
    }

    public function GetSimplePageContent($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $where = "$contentColumn[pageId] = $pageId";
        $content = DBUtil::selectObjectArray('content_content', $where);

        return $content;
    }

    protected function contentGetContent($mode, $id, $editing, $language, $translate, $orderBy = null)
    {
        $id = (int) $id;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];

        if ($mode == 'content') {
            $restriction = "$contentColumn[id] = $id";
        } else {
            $restriction = "$contentColumn[pageId] = $id";
        }
        if (!$editing) {
            $restriction .= " and c.$contentColumn[active] = 1 and c.$contentColumn[visiblefor] ".(UserUtil::isLoggedIn()?'<=1':'>=1');
        }

        $language = DataUtil::formatForStore($language);

        $cols = DBUtil::_getAllColumns('content_content');
        $ca = DBUtil::getColumnsArray('content_content');
        $ca[] = 'translated';

        $sql = "
SELECT $cols,
                $translatedColumn[data] AS translated
FROM $contentTable c
LEFT JOIN $translatedTable t
     ON     t.$translatedColumn[contentId] = $contentColumn[id]
        AND t.$translatedColumn[language] = '$language'
WHERE $restriction";

        if (empty($orderBy))
            $orderBy = "$contentColumn[areaIndex], $contentColumn[position]";

        $sql .= " ORDER BY $orderBy";

        $dbresult = DBUtil::executeSQL($sql);

        $content = DBUtil::marshallObjects($dbresult, $ca);

        for ($i = 0, $cou = count($content); $i < $cou; ++$i) {
            $c = &$content[$i];
            $c['data'] = (empty($c['data']) ? null : unserialize($c['data']));
            $c['translated'] = (empty($c['translated']) ? null : unserialize($c['translated']));

            if ($translate)
                if (is_array($c['translated']) && is_array($c['data']))
                    $c['data'] = array_merge($c['data'], $c['translated']);

            $contentPlugin = $this->getContentPlugin($c);
            if ($contentPlugin === false) {
                return LogUtil::registerError($this->__("Error! Can't load content plugin"));
            }
            $content[$i]['plugin'] = $contentPlugin;
            $content[$i]['isTranslatable'] = $contentPlugin->isTranslatable();
        }

        return $content;
    }

    public function getPageAndSubPageContent($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $pageTable = $table['content_page'];
        $pageColumn = $table['content_page_column'];
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        // Fetch all content items that belongs to page X or any of it's sub pages
        $sql = "
SELECT co.*
FROM $pageTable page
JOIN $pageTable subPage
     ON     subPage.$pageColumn[setLeft] >= page.$pageColumn[setLeft]
        AND subPage.$pageColumn[setRight] <= page.$pageColumn[setRight]
JOIN $contentTable co
     ON co.$contentColumn[pageId] = subPage.$pageColumn[id]
WHERE page.$pageColumn[id] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);

        $ca = DBUtil::getColumnsArray('content_content');
        $content = DBUtil::marshallObjects($dbresult, $ca);

        for ($i = 0, $cou = count($content); $i < $cou; ++$i) {
            $c = &$content[$i];
            $c['data'] = (empty($c['data']) ? null : unserialize($c['data']));
            $contentPlugin = $this->getContentPlugin($c);
            if ($contentPlugin === false)
                return LogUtil::registerError($this->__("Error! Can't load content plugin"));
            $content[$i]['plugin'] = $contentPlugin;
        }

        return $content;
    }

    /*=[ Create new content element ]================================================*/

    public function newContent($args)
    {
        $contentData = $args['content'];
        $pageId = (int) $args['pageId'];
        $contentAreaIndex = (int) $args['contentAreaIndex'];
        $position = (int) $args['position'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        if (!$this->contentMoveContentDown($position, $contentAreaIndex, $pageId)) {
            return false;
        }

        $contentPlugin = ModUtil::apiFunc($contentData['module'], 'contenttypes', $contentData['type'], null);

        $contentData['pageId'] = $pageId;
        $contentData['areaIndex'] = $contentAreaIndex;
        $contentData['position'] = $position;
        if (!isset($contentData['data']))
            $contentData['data'] = serialize($contentPlugin->getDefaultData());
        else
            $contentData['data'] = serialize($contentData['data']);

        DBUtil::insertObject($contentData, 'content_content', 'id', true); // true => preserve values (id-column)


        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTADDED' /* delayed translation */));
            if ($ok === false)
                return false;
        }

        contentClearCaches();
        return $contentData['id'];
    }

    protected function contentGetLastContentPosition($pageId, $contentAreaIndex)
    {
        $pageId = (int) $pageId;
        $contentAreaIndex = (int) $contentAreaIndex;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
SELECT MAX($contentColumn[position])
FROM $contentTable
WHERE $contentColumn[pageId] = $pageId";

        $pos = DBUtil::selectScalar($sql);
        return $pos === null ? -1 : (int) $pos;
    }

    /*=[ Clone content element on same page ]====================================================*/
    // TODO: maybe reuse in/with copyContentOfPageToPage
    public function cloneContent($args)
    {
        $contentId = (int)$args['id'];
        $cloneTranslation = isset($newPage['translation']) ? $newPage['translation'] : true;
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $contentData = DBUtil::selectObjectByID('content_content', $contentId);
        if ($contentData === false) {
            return false;
        }

        $searchableData = DBUtil::selectObjectByID('content_searchable', $contentId, 'contentId');

        $contentData['position']++;
        unset($contentData['id']);

        if ($cloneTranslation) {
            $tables = DBUtil::getTables();
            $translatedColumn = $tables['content_translatedcontent_column'];
            $translations = DBUtil::selectObjectArray('content_translatedcontent', $translatedColumn['contentId'].'='.$contentId);
        }

        if (!$this->contentMoveContentDown($contentData['position'], $contentData['areaIndex'], $contentData['pageId'])) {
            return false;
        }

        DBUtil::insertObject($contentData, 'content_content');

        if (!($searchableData === false)) {
            $searchableData['contentId'] = $contentData['id'];
            DBUtil::insertObject($searchableData, 'content_searchable');
        }

        if ($cloneTranslation && count($translations) > 0) {
            foreach ($translations as &$t) {
                $t['contentId'] = $contentData['id'];
            }
            DBUtil::insertObjectArray($translations, 'content_translatedcontent', 'contentId', true);
        }

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTADDED' /* delayed translation */));
            if ($ok === false) {
                return false;
            }
        }

        contentClearCaches();
        return $contentData['id'];
    }
    
    /*=[ Update content element ]====================================================*/
    public function updateContent($args)
    {
        $contentData = $args['content'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $contentData['id'] = $args['id'];
        if (isset($contentData['data']))
            $contentData['data'] = serialize($contentData['data']);

        DBUtil::updateObject($contentData, 'content_content');

        if (!empty($args['searchableText'])) {
            if (!$this->contentUpdateSearchableText((int) $args['id'], $args['searchableText']))
                return false;
        }

        $content = $this->getContent(array('id' => $contentData['id']));
        if ($content === false)
            return false;

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYCONTENTUPDATED' /* delayed translation */));
            if ($ok === false)
                return false;
        }

        contentClearCaches();
        return true;
    }

    protected function contentUpdateSearchableText($contentId, $text)
    {
        $table = DBUtil::getTables();
        $searchTable = $table['content_searchable'];
        $searchColumn = $table['content_searchable_column'];

        $sql = "DELETE FROM $searchTable WHERE $searchColumn[contentId] = $contentId";
        DBUtil::executeSQL($sql);

        $sql = "
INSERT INTO $searchTable
  ($searchColumn[contentId], $searchColumn[text])
VALUES
  ($contentId, '" . DataUtil::formatForStore($text) . "')";
        DBUtil::executeSQL($sql);

        return true;
    }

    /*=[ Copy content ]====================================================*/
    
    public function copyContentOfPageToPage($args)
    {    
        $fromPage = (int)$args['fromPageId'];
        $toPage = (int)$args['toPageId'];
        if ($fromPage <= 0 || $toPage <= 0) { return false; }
        $cloneTranslation = isset($args['cloneTranslation']) ? $args['cloneTranslation'] : true;
    
        $tables = DBUtil::getTables();
        $translatedColumn = $tables['content_translatedcontent_column'];

        $content = $this->GetSimplePageContent(array('pageId' => $fromPage));
        for ($i = 0; $i < count($content); $i++) {
            $contentData = $content[$i];
            $contentData['id'] = null;
            $contentData['pageId'] = $toPage;
            DBUtil::insertObject($contentData, 'content_content', 'id');
            if ($cloneTranslation) {
                $translations = DBUtil::selectObjectArray('content_translatedcontent', $translatedColumn['contentId'].'='.$contentData['id']);
                if (!($translations === false) && count($translations) > 0) {
                    foreach ($translations as &$t) {
                        $t['contentId'] = $contentData['id'];
                    }
                    DBUtil::insertObjectArray($translations, 'content_translatedcontent', 'contentId', true);
                }
            }
            $searchData = DBUtil::selectObjectByID('content_searchable', $contentData['id'], 'contentId');
            if ($searchData) {
                $searchData['contentId'] = $contentData['id'];
                DBUtil::insertObject($searchData, 'content_searchable');
            }
        }
        contentClearCaches();
        return true;
    }

    /*=[ Delete content element ]====================================================*/

    public function deleteContent($args)
    {
        $contentId = (int) $args['contentId'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $content = $this->getContent(array('id' => $contentId));
        if ($content === false)
            return false;

        $contentType = $this->getContentType($content);
        if ($contentType === false)
            return false;

        $contentType['plugin']->delete();

        if (!$this->contentRemoveContent($contentId))
            return false;

        DBUtil::deleteObjectByID('content_content', $contentId);

        $table = DBUtil::getTables();
        $searchTable = $table['content_searchable'];
        $searchColumn = $table['content_searchable_column'];

        $sql = "DELETE FROM $searchTable WHERE $searchColumn[contentId] = $contentId";
        DBUtil::executeSQL($sql);

        $ok = $this->deleteTranslation(array('contentId' => $contentId, 'includeHistory' => false));
        if ($ok === false)
            return false;

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYCONTENTDELETED' /* delayed translation */));
            if ($ok === false)
                return false;
        }

        contentClearCaches();
        return true;
    }

    public function deletePageAndSubPageContent($args)
    {
        $pageId = (int) $args['pageId'];

        // Get all content items on this page and all it's sub pages
        $contentItems = $this->getPageAndSubPageContent(array('pageId' => $pageId));
        if ($contentItems === false)
            return false;

        for ($i = 0, $cou = count($contentItems); $i < $cou; ++$i) {
            // Make sure content items get a chance to delete themselves
            $contentItems[$i]['plugin']->delete();

            // Delete from DB
            DBUtil::deleteObjectByID('content_content', $contentItems[$i]['id']);
        }

        contentClearCaches();
        return true;
    }

    /*=[ Translate content ]=========================================================*/

    public function updateTranslation($args)
    {
        $contentId = (int) $args['contentId'];
        $language = DataUtil::formatForStore($args['language']);
        $translated = $args['translated'];
        $addVersion = isset($args['addVersion']) ? $args['addVersion'] : true;

        $table = DBUtil::getTables();
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];

        // Delete optional existing translation
        $where = "$translatedColumn[contentId] = $contentId AND $translatedColumn[language] = '$language'";
        DBUtil::deleteWhere('content_translatedcontent', $where);

        // Insert new
        $translatedData = array('contentId' => $contentId, 'language' => $language, 'data' => serialize($translated));
        DBUtil::insertObject($translatedData, 'content_translatedcontent');

        $content = $this->getContent(array('id' => $contentId));
        if ($content === false)
            return false;

        if ($addVersion) {
            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYTRANSLATED' /* delayed translation */));
            if ($ok === false)
                return false;
        }

        contentClearCaches();
        return true;
    }

    public function deleteTranslation($args)
    {
        $contentId = (int) $args['contentId'];
        $language = isset($args['language']) ? $args['language'] : null;
        $includeHistory = isset($args['includeHistory']) ? $args['includeHistory'] : true;

        $table = DBUtil::getTables();
        $translatedColumn = $table['content_translatedcontent_column'];

        // Delete existing translation
        if ($language != null)
            $where = "$translatedColumn[contentId] = $contentId AND $translatedColumn[language] = '" . DataUtil::formatForStore($language) . "'";
        else
            $where = "$translatedColumn[contentId] = $contentId";

        DBUtil::deleteWhere('content_translatedcontent', $where);

        // Get content to find page ID
        if ($includeHistory) {
            $content = $this->getContent(array('id' => $contentId));
            if ($content === false)
                return false;

            $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $content['pageId'], 'action' => '_CONTENT_HISTORYTRANSLATIONDEL' /* delayed translation */));
            if ($ok === false)
                return false;
        }

        contentClearCaches();
        return true;
    }

    public function deletePageTranslations($args)
    {
        $pageId = (int) $args['pageId'];
        $language = isset($args['language']) ? $args['language'] : null;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];

        if ($language != null)
            $restriction = "AND t.$translatedColumn[language] = '" . DataUtil::formatForStore($language) . "'";
        else
            $restriction = '';

        $sql = "
DELETE t
FROM $translatedTable t, $contentTable c
WHERE     t.$translatedColumn[contentId] = c.$contentColumn[id]
                $restriction
      AND c.$contentColumn[pageId] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);

        contentClearCaches();
        return true;
    }

    public function getTranslationInfo($args)
    {
        $contentId = (isset($args['contentId']) ? (int) $args['contentId'] : null);
        $pageId = (isset($args['pageId']) ? (int) $args['pageId'] : null);

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        // fetch content + page info


        if ($contentId != null) {
            $contentItem = $this->getContent(array('id' => $contentId));
            if ($contentItem === false)
                return false;

            $pageId = $contentItem['pageId'];
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $pageId));
        if ($page === false)
            return false;

        $layout = ModUtil::apiFunc('Content', 'Layout', 'getLayoutPlugin', array('layout' => $page['layout']));
        if ($layout === false)
            return false;

        $contentItems = $this->contentGetContent('page', $pageId, $editing, null, false);
        if ($contentItems === false)
            return false;

        $translatableItems = array();
        foreach ($contentItems as $item) {
            if ($item['plugin']->isTranslatable())
                $translatableItems[] = $item;
        }

        $translationItems = array();
        $i = 1;
        $count = count($translatableItems);
        $currentIndex = -1;
        foreach ($translatableItems as $item) {
            if ($item['plugin']->isTranslatable()) {
                $translationItems[] = array('text' => $layout->getContentAreaTitle($item['areaIndex']) . ": $item[type] ($i/$count)", 'value' => $item['id']);
                if ($item['id'] == $contentId)
                    $currentIndex = $i - 1;
                ++$i;
            }
        }

        $nextContentId = null;
        $prevContentId = null;

        if ($contentId != null) {
            if ($currentIndex < count($translationItems) - 1)
                $nextContentId = $translatableItems[$currentIndex + 1]['id'];
            if ($currentIndex > 0)
                $prevContentId = $translatableItems[$currentIndex - 1]['id'];
        } else {
            if (count($translatableItems) > 0)
                $nextContentId = $translatableItems[0]['id'];
        }

        return array('items' => $translationItems, 'nextContentId' => $nextContentId, 'prevContentId' => $prevContentId);
    }

    public function getTranslations($args)
    {
        $pageId = (int) $args['pageId'];

        $table = DBUtil::getTables();
        $translatedTable = $table['content_translatedcontent'];
        $translatedColumn = $table['content_translatedcontent_column'];
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $cols = DBUtil::_getAllColumns('content_translatedcontent');
        $ca = DBUtil::getColumnsArray('content_translatedcontent');

        $sql = "
SELECT $cols
FROM $translatedTable t
LEFT JOIN $contentTable c
     ON c.$contentColumn[id] = t.$translatedColumn[contentId]
WHERE c.$contentColumn[pageId] = $pageId";

        $dbresult = DBUtil::executeSQL($sql);

        $translations = DBUtil::marshallObjects($dbresult, $ca);

        return $translations;
    }

    /*=[ Moving content ]============================================================*/

    public function dragContent($args)
    {
        if (!isset($args['pageId']) || !isset($args['contentId']) || !isset($args['contentAreaIndex']) || !isset($args['position'])) {
            return LogUtil::registerArgsError();
        }

        $pageId = (int) $args['pageId'];
        $contentId = (int) $args['contentId'];
        $contentAreaIndex = (int) $args['contentAreaIndex'];
        $position = (int) $args['position'];

        if (!$this->contentRemoveContent($contentId)) {
            return false;
        }
        if (!$this->contentInsertContent($contentId, $position, $contentAreaIndex, $pageId)) {
            return false;
        }
        $ok = ModUtil::apiFunc('Content', 'History', 'addPageVersion', array('pageId' => $pageId, 'action' => '_CONTENT_HISTORYCONTENTMOVED' /* delayed translation */));
        if ($ok === false) {
            return false;
        }
        
        contentClearCaches();
        return true;
    }

    // Remove content from content area, but do not delete it
    protected function contentRemoveContent($contentId)
    {
        $contentData = $this->getContent(array('id' => $contentId));
        if ($contentData === false)
            return false;

        $pageId = (int) $contentData['pageId'];
        $contentAreaIndex = (int) $contentData['areaIndex'];
        $position = (int) $contentData['position'];

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
UPDATE $contentTable
SET $contentColumn[position] = $contentColumn[position]-1
WHERE     $contentColumn[pageId] = $pageId
      AND $contentColumn[areaIndex] = $contentAreaIndex
      AND $contentColumn[position] > $position";

        DBUtil::executeSQL($sql);

        contentClearCaches();
        return true;
    }

    // Insert content in content area
    protected function contentInsertContent($contentId, $position, $contentAreaIndex, $pageId)
    {
        $contentData = $this->getContent(array('id' => $contentId));
        if ($contentData === false)
            return false;

        if (!$this->contentMoveContentDown($position, $contentAreaIndex, $pageId))
            return false;

        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $contentData = array('id' => $contentId, 'position' => $position, 'areaIndex' => $contentAreaIndex);
        DBUtil::updateObject($contentData, 'content_content');

        contentClearCaches();
        return true;
    }

    protected function contentMoveContentDown($position, $contentAreaIndex, $pageId)
    {
        $table = DBUtil::getTables();
        $contentTable = $table['content_content'];
        $contentColumn = $table['content_content_column'];

        $sql = "
UPDATE $contentTable
SET $contentColumn[position] = $contentColumn[position]+1
WHERE     $contentColumn[pageId] = $pageId
      AND $contentColumn[areaIndex] = $contentAreaIndex
      AND $contentColumn[position] >= $position";

        DBUtil::executeSQL($sql);

        contentClearCaches();
        return true;
    }

    /*=[ Scanning and loading content type plugins ]=================================*/

    function getContentPlugins($args)
    {
        $modules = ModUtil::getAllMods();
        $plugins = array();
        foreach ($modules as $module) {
            if (ModUtil::loadApi($module['name'], 'contenttypes')) {
//                $dir = "modules/$module[directory]/lib/$module[directory]/ContentType";
// TODO: Find a new solution for this plugin directory!!!
                $dir = "modules/$module[directory]/pncontenttypesapi";
                if (is_dir($dir) && $dh = opendir($dir)) {
                    while (($filename = readdir($dh)) !== false) {
                        if (preg_match('/^([a-zA-Z0-9_]+).php$/', $filename, $matches)) {
                            $contentTypeName = $matches[1];
                            // check permissions for this contentType plugin
                            if (SecurityUtil::checkPermission('Content:plugins:content', $contentTypeName . '::', ACCESS_READ)) {
                                $plugins[] = ModUtil::apiFunc($module['name'], 'contenttypes', $contentTypeName, null);
                            }
                        }
                    }

                    closedir($dh);
                }
            }
        }

        usort($plugins, array($this, 'contentPluginCompare'));

        return $plugins;
    }

    protected function contentPluginCompare($a, $b)
    {
        return strcmp($a->getTitle(), $b->getTitle());
    }

    public function getContentTypes($args)
    {
        $includeInactive = isset($args['includeInactive']) ? $args['includeInactive'] : false;
        $plugins = $this->getContentPlugins(array());
        $contentTypes = array();

        for ($i = 0, $cou = count($plugins); $i < $cou; ++$i) {
            $plugin = &$plugins[$i];
            if ($includeInactive || $plugin->isActive()) {
                $contentTypes[] = array('module' => $plugin->getModule(), 'name' => $plugin->getName(), 'title' => $plugin->getTitle(), 'description' => $plugin->getDescription(), 'adminInfo' => $plugin->getAdminInfo(), 'isActive' => $plugin->isActive());
            }
        }

        return $contentTypes;
    }

    public function getContentPlugin($args)
    {
        $plugin = ModUtil::apiFunc($args['module'], 'contenttypes', $args['type'], $args);
        if (empty($plugin)) {
            if (!ModUtil::available($args['module'])) {
                return LogUtil::registerError($this->__f('Error! Unable to load plugin [%1$s] in module [%2$s] since the module is not available.', array($args[type], $args[module])));
            }
            return LogUtil::registerError($this->__f('Error! Unable to load plugin [%1$s] in module [%2$s] for some unknown reason.', array($args[type], $args[module])));
        }
        $plugin->contentId = $args['id'];
        $plugin->pageId = $args['pageId'];
        $plugin->contentAreaIndex = $args['areaIndex'];
        $plugin->position = $args['position'];
        $plugin->stylePosition = $args['stylePosition'];
        $plugin->styleWidth = $args['styleWidth'];
        $plugin->styleClass = $args['styleClass'];
        if (isset($args['data'])) {
            $plugin->loadData($args['data']);
        }
        return $plugin;
    }

    public function getContentType($args)
    {
        $plugin = $this->getContentPlugin($args);
        if ($plugin === false) {
            return false;
        }
        return array('plugin' => &$plugin, 'module' => $plugin->getModule(), 'name' => $plugin->getName(), 'title' => $plugin->getTitle(), 'description' => $plugin->getDescription(), 'adminInfo' => $plugin->getAdminInfo(), 'isActive' => $plugin->isActive());
    }
}