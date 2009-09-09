<?php
/**
 * Content
 *
 * @copyright (c) 2001-now, Frank Schummertz
 * @link http://code.zikula.org/content
 * @version $Id:  $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Content
 */

/**
 * Content needle
 * @param $args['nid'] needle id
 * @return array()
 */
function content_needleapi_content($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);
    
    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('Content')) {
                
                $contentpage = pnModAPIFunc('content', 'page', 'getPage',
                                            array('id'             => $nid,
                                                  'includeContent' => false));
                if ($contentpage <> false) {
                    $cache[$nid] = '<a href="' . DataUtil::formatForDisplay(pnModURL('Content', 'user', 'view', array('pid' => $nid))) . '" title="' . DataUtil::formatForDisplay($contentpage['title']) . '">' . DataUtil::formatForDisplay($contentpage['title']) . '</a>'; 
                } else {
                    $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_CONTENT_NEEDLE_UNKNOWNID) . '</em>';
                }
            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(_CONTENT_NEEDLE_NOTAVAILABLE) . '</em>';
            }    
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(_CONTENT_NEEDLE_NONEEDLEID) . '</em>';
    }
    return $result;
    
}
