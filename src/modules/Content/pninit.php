<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function content_init()
{
    $dom = ZLanguage::getModuleDomain('content');

    if (!DBUtil::createTable('content_page')) {
        return false;
    }
    if (!DBUtil::createTable('content_content')) {
        return false;
    }
    if (!DBUtil::createTable('content_pagecategory')) {
        return false;
    }
    if (!DBUtil::createTable('content_searchable')) {
        return false;
    }
    if (!DBUtil::createTable('content_translatedpage')) {
        return false;
    }
    if (!DBUtil::createTable('content_translatedcontent')) {
        return false;
    }
    if (!DBUtil::createTable('content_history')) {
        return false;
    }

    if (!_content_setCategoryRoot()) {
        LogUtil::registerStatus(__('Warning! Could not create the default Content category tree. If you want to use categorisation with Content, register at least one property for the module in the Category Registry.', $dom)); 
    }

    pnModSetVar('content', 'shorturlsuffix', '.html');
    pnModSetVar('content', 'styleClasses', "greybox|Grey box\nredbox|Red box\nyellowbox|Yellow box\ngreenbox|Green box");
    pnModSetVar('content', 'enableVersioning', false);
    pnModSetVar('content', 'flickrApiKey', '');
    pnModSetVar('content', 'googlemapApiKey', '');
    pnModSetVar('content', 'categoryUsage', '1');
    pnModSetVar('content', 'categoryPropPrimary', 'primary');
    pnModSetVar('content', 'categoryPropSecondary', 'primary');
    pnModSetVar('content', 'newPageState', '1');
    pnModSetVar('content', 'countViews', '0');

    // create the default data for the Content module
    content_defaultdata();        

    return true;
}

function _content_setCategoryRoot()
{
    // load necessary classes
    Loader::loadClass('CategoryUtil');
    Loader::loadClassFromModule('Categories', 'Category');
    Loader::loadClassFromModule('Categories', 'CategoryRegistry');

    $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Global');
    if ($rootcat) {
        // create an entry in the categories registry
        $registry = new PNCategoryRegistry();
        $registry->setDataField('modname', 'content');
        $registry->setDataField('table', 'content_page');
        $registry->setDataField('property', 'primary');
        $registry->setDataField('category_id', $rootcat['id']);
        $registry->insert();
    }

    return true;
}

// -----------------------------------------------------------------------
// Module upgrade
// -----------------------------------------------------------------------


function content_upgrade($oldVersion)
{
    $ok = true;

    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case '0.0.0':
        case '1.0.0':
        case '1.1.0':
            $ok = $ok && contentUpgrade_1_2_0($oldVersion);
        case '1.2.0':
            $ok = $ok && contentUpgrade_1_2_0_1($oldVersion);
        case '1.2.0.1':
        case '2.0.0':
        case '2.0.1':
            $ok = $ok && contentUpgrade_2_0_2($oldVersion);
        case '2.0.2':
        case '2.1.0':
            $ok = $ok && contentUpgrade_2_1_1($oldVersion);
        case '2.1.1':
            $ok = $ok && contentUpgrade_2_1_2($oldVersion);
        case '2.1.2':
        case '3.0.0':
        case '3.0.1':
        case '3.0.2':
        case '3.0.3':
            $ok = $ok && contentUpgrade_3_1_0($oldVersion);
        case '3.1.0':
            $ok = $ok && contentUpgrade_3_2_0($oldVersion);
        case '3.2.0':
            $ok = $ok && contentUpgrade_3_2_1($oldVersion);
        // future
    }

    // Update successful
    return $ok;
}

function contentUpgrade_1_2_0($oldVersion)
{
    if (!DBUtil::createTable('content_translatedcontent')) {
        return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedcontent': " . $dbconn->ErrorMsg());
    }
    if (!DBUtil::createTable('content_translatedpage')) {
        return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedpage': " . $dbconn->ErrorMsg());
    }
    if (!pnModSetVar('content', 'shorturlsuffix', '.html')) {
        return false;
    }

    return true;
}

function contentUpgrade_1_2_0_1($oldVersion)
{
    // Drop unused version 1.x column. Some people might have done this manually, so ignore errors.
    $dbconn = DBConnectionStack::getConnection();
    $pntables = pnDBGetTables();
    $dict = NewDataDictionary($dbconn);
    $table = $pntables['content_content'];
    $sqlarray = $dict->DropColumnSQL($table, array('con_language'));
    $dict->ExecuteSQLArray($sqlarray);
    return true;
}

function contentUpgrade_2_0_2($oldVersion)
{
    DBUtil::changeTable('content_content');
    pnModSetVar('content', 'styleClasses', "greybox|Grey box\nredbox|Red box");
    return true;
}

function contentUpgrade_2_1_1($oldVersion)
{
    if (!DBUtil::createTable('content_history')) {
        return false;
    }
    return true;
}

function contentUpgrade_2_1_2($oldVersion)
{
    // Add language column (again since version 1.2.0.1)
    DBUtil::changeTable('content_page');

    $dbconn = DBConnectionStack::getConnection();
    $pntables = pnDBGetTables();
    $language = ZLanguage::getLanguageCode();

    // Assume language of created pages is same as current lang
    $table = $pntables['content_page'];
    $column = $pntables['content_page_column'];
    $sql = "UPDATE $table SET $column[language] = '" . DataUtil::formatForStore($language) . "'";
    DBUtil::executeSQL($sql);

    return true;
}

function contentUpgrade_3_1_0($oldVersion)
{
    $tables = pnDBGetTables();

    // fix serialisations
    foreach (array('content' => 'id', 'history' => 'id', 'translatedcontent' => 'contentId') as $table => $idField) {
        $obj = DBUtil::selectObjectArray('content_' . $table);
        foreach ($obj as $contentItem) {
            $data = DataUtil::mb_unserialize($contentItem['data']);
            $contentItem['data'] = serialize($data);
            DBUtil::updateObject($contentItem, 'content_' . $table, '', $idField, true);
        }
    }

    // fix language codes
    foreach (array('page' => 'id', 'translatedcontent' => 'contentId', 'translatedpage' => 'pageId') as $table => $idField) {
        $obj = DBUtil::selectObjectArray('content_' . $table);
        if (!count($obj)) {
            continue;
        }
        foreach ($obj as $contentItem) {
            // translate l3 -> l2
           $l2 = ZLanguage::translateLegacyCode($contentItem['language']);
            if (!$l2) {
                continue;
            }
            $sql = 'UPDATE ' . $tables['content_' . $table] . ' a SET a.' . $tables['content_' . $table . '_column']['language'] . ' = \'' . $l2 . '\' WHERE a.' . $tables['content_' . $table . '_column'][$idField] . ' = \'' . $contentItem[$idField] . '\'';
            DBUtil::executeSQL($sql);
        }
    }
    return true;
}

function contentUpgrade_3_2_0($oldVersion)
{
    // update the database
    DBUtil::changeTable('content_page');
    DBUtil::changeTable('content_content');
    DBUtil::changeTable('content_translatedpage');
    DBUtil::changeTable('content_translatedcontent');
    DBUtil::changeTable('content_history');
    
    // add new variable(s)
    pnModSetVar('content', 'categoryUsage', '1');
    pnModSetVar('content', 'categoryPropPrimary', 'primary');
    pnModSetVar('content', 'categoryPropSecondary', 'primary');
    pnModSetVar('content', 'newPageState', '1');

    // clear compiled templates and News cache
    pnModAPIFunc('pnRender', 'user', 'clear_compiled');
    pnModAPIFunc('pnRender', 'user', 'clear_cache', array('module' => 'content'));
    
    return true;
}

function contentUpgrade_3_2_1($oldVersion)
{
    // update the database
    DBUtil::changeTable('content_page');
    
    // add new variable(s)
    pnModSetVar('content', 'countViews', '0');

    return true;
}

// -----------------------------------------------------------------------
// Module delete
// -----------------------------------------------------------------------
function content_delete()
{
    DBUtil::dropTable('content_page');
    DBUtil::dropTable('content_content');
    DBUtil::dropTable('content_pagecategory');
    DBUtil::dropTable('content_searchable');
    DBUtil::dropTable('content_translatedcontent');
    DBUtil::dropTable('content_translatedpage');
    DBUtil::dropTable('content_history');

    pnModDelVar('content');

    // Deletion successful
    return true;
}

// -----------------------------------------------------------------------
// Module default data (intro page)
// -----------------------------------------------------------------------
function content_defaultdata() 
{
    $dom = ZLanguage::getModuleDomain('content');

    // create one page with 2 columns and some content
    $page = array('title'   => __('Content introduction page', $dom),
            'urlname'       => __('content-introduction-page', $dom),
            'layout'        => 'column2_6238_header',
            'setLeft'       => '0',
            'setRight'      => '1',
            'language'      => ZLanguage::getLanguageCode());
    
    // Insert the default page
    if (!($obj = DBUtil::insertObject($page, 'content_page'))) {
        LogUtil::registerStatus(__('Warning! Could not create the default Content introductory page.', $dom));
    } else {
        // create the contentitems for this page
        $content = array();
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '0',
                'position'          => '0',
                'module'            => 'content',
                'type'              => 'heading',
                'data'              => serialize(array('text' => __('A Content page consists of various content items in a chosen layout', $dom),
                                            'headerSize' => 'h3')));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '1',
                'position'          => '0',
                'module'            => 'content',
                'type'              => 'html',
                'data'              => serialize(array('text' => __('<p>Each created page has a specific layout, like 1 column with and without a header, 2 columns, 3 columns. The chosen layout contains various content areas. In each area you can place 1 or more content items of various kinds like:</p> <ul> <li>HTML text;</li> <li>YouTube videos;</li> <li>Google maps;</li> <li>Flickr photos;</li> <li>RSS feeds;</li> <li>Computer Code;</li> <li>the output of another Zikula module.</li> </ul> <p>Within these content areas you can sort the content items by means of drag & drop.<br /> You can make an unlimited number of pages and structure them hierarchical. Your page structure can be displayed in a multi level menu in your website.</p>', $dom),
                                            'inputType' => 'text')));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '1',
                'position'          => '1',
                'module'            => 'content',
                'type'              => 'html',
                'data'              => serialize(array('text' => __('<p><strong>This is a second HTML text content item in the left column</strong><br /> Content is an extendible module. You can create your own content plugins and layouts and other Zikula modules can also offer content items. The News published module for instance has a Content plugin for a list of the latest articles.</p>', $dom),
                                            'inputType' => 'text')));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '2',
                'position'          => '0',
                'module'            => 'content',
                'type'              => 'quote',
                'data'              => serialize(array('text' => __('No matter what your needs, Zikula can provide the solution.', $dom),
                                            'source' => 'http://zikula.org', 'desc' => 'Zikula homepage')));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '2',
                'position'          => '1',
                'module'            => 'content',
                'type'              => 'computercode',
                'data'              => serialize(array('text' => __('$this->doAction($var); // just some code', $dom))));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '2',
                'position'          => '2',
                'module'            => 'content',
                'type'              => 'html',
                'data'              => serialize(array('text' => __('<p>So you see that you can place all kinds of content on the page in your own style and liking. This makes Content a really powerful module.</p> <p>This page uses the <strong>2 column (62|38) layout</strong> which has a header, 2 colums with 62% width on the left and 38% width on the right and a footer</p>', $dom),
                                            'inputType' => 'text')));
        $content[] = array('pageId' => $obj['id'],
                'areaIndex'         => '3',
                'position'          => '0',
                'module'            => 'content',
                'type'              => 'html',
                'data'              => serialize(array('text' => __('This <strong>footer</strong> finishes of this introduction page. Good luck with using Content. The <a href="index.php?module=content&type=edit">Page list</a> interface lets you edit or delete this introduction page. In the <a href="index.php?module=content&type=admin">administration</a> interface you can further control the Content module.', $dom),
                                            'inputType' => 'text')));

        // write the items to the dbase
        foreach ($content as $contentitem) {
            DBUtil::insertObject($contentitem, 'content_content');
        }
    }
}
// -----------------------------------------------------------------------

