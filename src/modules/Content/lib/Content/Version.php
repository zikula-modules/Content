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
class Content_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['version']        = '4.0.0';
        $meta['oldnames']       = array('content');
        $meta['displayname']    = $this->__('Content editing');
        $meta['description']    = $this->__('Content is a page editing module. With it you can insert and edit various content items, such as HTML texts, videos, Google maps and much more.');
        // this defines the module's url and should be in lowercase without space
        $meta['url']            = $this->__('content');
        $meta['core_min']       = '1.3.0'; // requires minimum 1.3.0 or later
        $meta['capabilities']   = array(HookUtil::SUBSCRIBER_CAPABLE => array('enabled' => true));
        $meta['securityschema'] = array('Content::' => '::',
                'Content:plugins:layout' => 'Layout name::',
                'Content:plugins:content' => 'Content type name::',
                'Content:page:' => 'Page id::');
        // Module depedencies
        $meta['dependencies'] = array(
                array('modname'    => 'Scribite',
                      'minversion' => '4.2.1',
                      'maxversion' => '',
                      'status'     => ModUtil::DEPENDENCY_RECOMMENDED),
        );
        return $meta;
    }

    protected function setupHookBundles()
    {
        // Register hooks for pages
        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber_area.ui.content.pages', 'ui', $this->__('Content Display Hooks'));
        $bundle->addType('ui.view', 'content.hook.pages.ui.view');
        $bundle->addType('ui.edit', 'content.hook.pages.ui.edit');
        $bundle->addType('ui.delete', 'content.hook.pages.ui.delete');
        $bundle->addType('validate.edit', 'content.hook.pages.validate.edit');
        $bundle->addType('validate.delete', 'content.hook.pages.validate.delete');
        $bundle->addType('process.edit', 'content.hook.pages.process.edit');
        $bundle->addType('process.delete', 'content.hook.pages.process.delete');
        $this->registerHookSubscriberBundle($bundle);

        $bundle = new Zikula_HookManager_SubscriberBundle($this->name, 'subscriber_area.fitler.content.pages', 'filter', $this->__('Content Filter Hooks'));
        $bundle->addType('ui.filter', 'content.hook.pages.ui.filter');
        $this->registerHookSubscriberBundle($bundle);
    }
}
