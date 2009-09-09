<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function content_init()
{
  if (!DBUtil::createTable('content_page'))
      return false;
  if (!DBUtil::createTable('content_content'))
      return false;
  if (!DBUtil::createTable('content_pagecategory'))
      return false;
  if (!DBUtil::createTable('content_searchable'))
      return false;
  if (!DBUtil::createTable('content_translatedpage')) 
      return false;
  if (!DBUtil::createTable('content_translatedcontent')) 
      return false;
  if (!DBUtil::createTable('content_history')) 
      return false;
  
  if (!_content_setCategoryRoot())
    return false;

  if (!pnModSetVar('content', 'shorturlsuffix', '.html'))
  	return LogUtil::registerError("Failed to set shorturlsuffix");
    
  return true;
}


function _content_setCategoryRoot()
{
  // load necessary classes
  Loader::loadClass('CategoryUtil');
  Loader::loadClassFromModule('Categories', 'Category');
  Loader::loadClassFromModule('Categories', 'CategoryRegistry');

  $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Global');
  if ($rootcat) 
  {
    // create an entry in the categories registry
    $registry = new PNCategoryRegistry();
    $registry->setDataField('modname', 'Content');
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
  switch($oldVersion)
  {
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
      // future
  }

    // Update successful
  return $ok;
}


function contentUpgrade_1_2_0($oldVersion)
{
  if (!DBUtil::createTable('content_translatedcontent'))
    return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedcontent': " . $dbconn->ErrorMsg());
  
  if (!DBUtil::createTable('content_translatedpage'))
    return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedpage': " . $dbconn->ErrorMsg());
  
  if (!pnModSetVar('content', 'shorturlsuffix', '.html'))
    return false;	
  
  return true;
}


function contentUpgrade_1_2_0_1($oldVersion)
{
  // Drop unused version 1.x column. Some people might have done this manually, so ignore errors.
  $dbconn   = DBConnectionStack::getConnection();
  $pntables = pnDBGetTables();
  $dict     = NewDataDictionary($dbconn);
  $table    = $pntables['content_content'];
  $sqlarray = $dict->DropColumnSQL ($table, array('con_language'));
  
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
  if (!DBUtil::createTable('content_history')) 
    return false;
  return true;
}


function contentUpgrade_2_1_2($oldVersion)
{
  // Add language column (again since version 1.2.0.1)
  DBUtil::changeTable('content_page');

  $dbconn   = DBConnectionStack::getConnection();
  $pntables = pnDBGetTables();
  $language = pnUserGetLang();

  // Assume language of created pages is same as current lang
  $table    = $pntables['content_page'];
  $column   = $pntables['content_page_column'];
  $sql = "UPDATE $table SET $column[language] = '" . DataUtil::formatForStore($language) . "'";
  DBUtil::executeSQL($sql);
  
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
