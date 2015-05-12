<?php
/**
 * Content tab navigation plugin
 *
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_ContentType_TabNavigation extends Content_AbstractContentType
{
    protected $tabTitles;
    protected $tabLinks;
    protected $contentItemIds;
    protected $tabType;
    protected $tabStyle;

    public function getTabTitles()
    {
        return $this->tabTitles;
    }
    public function setTabTitles($tabTitles)
    {
        $this->tabTitles = $tabTitles;
    }

    public function getTabLinks()
    {
        return $this->tabLinks;
    }
    public function setTabLinks($tabLinks)
    {
        $this->tabLinks = $tabLinks;
    }

    public function getContentItemIds()
    {
        return $this->contentItemIds;
    }
    public function setContentItemIds($contentItemIds)
    {
        $this->contentItemIds = $contentItemIds;
    }

    public function getTabType()
    {
        return $this->tabType;
    }
    public function setTabType($tabType)
    {
        $this->tabType = $tabType;
    }

    public function getTabStyle()
    {
        return $this->tabStyle;
    }
    public function setTabStyle($tabStyle)
    {
        $this->tabStyle = $tabStyle;
    }

    public function getTitle()
    {
        return $this->__('Tab Navigation');
    }
    
    public function getDescription()
    {
        return $this->__('Tab Navigation with existing Content items.');
    }
    
    public function isTranslatable()
    {
        return false;
    }
    
    public function loadData(&$data)
    {
        $this->tabTitles = $data['tabTitles'];
        $this->tabLinks = $data['tabLinks'];
        $this->contentItemIds = $data['contentItemIds'];
        $this->tabType = $data['tabType'];
        $this->tabStyle = $data['tabStyle'];
    }
    
    public function display()
    {
        // Convert the variables into arrays
        $contentItemIds = explode(';', str_replace(' ', '', $this->contentItemIds));
        $tabTitles = explode(';', $this->tabTitles);
        $tabLinks = explode(';', str_replace(' ', '', $this->tabLinks));

        // Make an array with output display of the Content items to tab
        $itemsToTab = array();
        foreach ($contentItemIds as $key => $contentItemId) {
            if (($contentItem = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $contentItemId))) != false) {
                $itemsToTab[$key]['display'] = $contentItem['plugin']->displayStart() . $contentItem['plugin']->display() . $contentItem['plugin']->displayEnd();
                $itemsToTab[$key]['title'] = $tabTitles[$key];
                $itemsToTab[$key]['link'] = isset($tabLinks[$key]) ? $tabLinks[$key] : 'tab'.$key;
            }
        }

        // assign variables and call the template
        $this->view->assign('itemsToTab', $itemsToTab);
        $this->view->assign('tabType', $this->tabType);
        $this->view->assign('tabStyle', $this->tabStyle);
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }

    public function displayEditing()
    {
        $output = '<h3>' . $this->__f('Tab navigation of Content items %s', $this->contentItemIds) . '</h3>';
        $output .= '<p>';
        switch($this->tabType) {
            case 1:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-tabs');
            break;
            case 2:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-pills');
            break;
            case 3:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-pills nav-stacked (col-sm3/col-sm-9)');
            break;
            case 4:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Zikula.UI Tabs');
            break;
        }
        $output .= '<br />' . $this->__('You can disable the individual Content Items if you only want to display them in this Tab Navigation.');
        $output .= '</p>';
        return $output;
    }

    function startEditing()
    {
        // options for choosing the tab navigation
        $tabTypeOptions = array( array('text' => $this->__('Bootstrap - nav nav-tabs'), 'value' => '1'),
            array('text' => $this->__('Bootstrap - nav nav-pills'), 'value' => '2'),
            array('text' => $this->__('Bootstrap - nav nav-pills nav-stacked (col-sm3/col-sm-9)'), 'value' => '3'),
            array('text' => $this->__('Zikula.UI Tabs'), 'value' => '4') );
        $this->view->assign('tabTypeOptions', $tabTypeOptions);
    }

    public function getDefaultData()
    {
        return array(
            'tabTitles' => '',
            'tabLinks' => '',
            'contentItemIds' => '',
            'tabType' => '',
            'tabStyle' => ''
        );
    }

    public function getSearchableText()
    {
        return;
    }
}