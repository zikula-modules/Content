<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

// The following information is used by the Modules module
// for display and upgrade purposes
class Content_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta = array();
        $meta['version']        = '4.0.0';
        $meta['oldnames']       = array('content');
        $meta['displayname']    = $this->__('Content editing');
        $meta['description']    = $this->__('Content is a page editing module. With it you can insert and edit various content items, such as HTML texts, videos, Google maps and much more.');
        //! module url should be different to displayname and in lowercase without space
        $meta['url']            = $this->__('content');
        $meta['securityschema'] = array('Content::' => '::',
                'Content:plugins:layout' => 'Layout name::',
                'Content:plugins:content' => 'Content type name::',
                'Content:page:' => 'Page id::');
        return $meta;
    }
}
