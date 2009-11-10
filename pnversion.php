<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

// The following information is used by the Modules module
// for display and upgrade purposes
$dom = ZLanguage::getModuleDomain('content');
$modversion['name']           = 'content';
// the version string must not exceed 10 characters!
$modversion['version']        = '3.0.4';
$modversion['description']    = __('Content editing module', $dom);
$modversion['displayname']    = __('Content', $dom);
//! module url should be different to displayname and in lowercase without space
$modversion['url']    = __('content', $dom);

// The following in formation is used by the credits module
// to display the correct credits
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['credits']        = 'pndocs/readme.txt';
$modversion['help']           = 'pndocs/readme.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Jorn Wildt';
$modversion['contact']        = 'http://www.elfisk.dk/';
$modversion['admin']          = 1;

// This one adds the info to the DB, so that users can click on the
// headings in the permission module
$modversion['securityschema'] = array('Content::' => '::', 'Content:plugins:layout' => 'Layout name::', 'Content:plugins:content' => 'Content type name::');
