<?php

class Content_Migration_ContentExpress extends Content_Migration_AbstractModule
{
    private $recordCount = 0;
    private $recordLevels = array();
    
    public function __construct()
    {
        $this->dataTable = 'ce_contentitems';
        $this->categoryTable = 'ce_categories';
        $this->migrateCategories = true;
        $this->pageMap[$this->rootPageLocalId] = 0;
        parent::__construct();
    }

    protected function getCategories()
    {
        $sql = "SELECT mc_id, mc_parent_id, mc_title FROM " . $this->tablePrefix . "_" . $this->categoryTable . " ORDER BY mc_parent_id, mc_id";
        $result = DBUtil::executeSQL($sql);
        $categories = DBUtil::marshallObjects($result);
        $reformattedArray = array();
        foreach ($categories as $category) {
            if ($category['mc_parent_id'] == '-1') {
                $category['mc_parent_id'] = 0;
            }
            $reformattedArray[] = array(
                'id' => $category['mc_id'],
                'pid' => $category['mc_parent_id'],
                'title' => $category['mc_title'],
            );
        }
        return $reformattedArray;
    }

    protected function createRecords($pid = -1, $lvl = 0)
    {
        $this->recordLevels[$pid] = $lvl;
        $sql = "SELECT * FROM " . $this->tablePrefix . "_" . $this->dataTable . " WHERE mc_parent_id=$pid ORDER BY mc_id";
        $result = DBUtil::executeSQL($sql);
        $pages = DBUtil::marshallObjects($result);
        $fieldmap = $this->getFieldMap();
        $i = 0;
        foreach ($pages as $page) {
            // correct values to Content appropriate types
            $page['mc_parent_id'] = ($page['mc_parent_id'] == -1) ? 0 : $page['mc_parent_id'];
            $page['active'] = $page['active'] - 1;
            foreach ($fieldmap as $newfield => $oldfield) {
                $this->records[$this->recordCount][$newfield] = $page[$oldfield];
            }
            $this->records[$this->recordCount]['position'] = $i;
            $this->records[$this->recordCount]['level'] = $this->recordLevels[$page['mc_parent_id']];
            $this->recordCount++;
            $i++;
            // create recursive records for all parent categories
            $this->createRecords($page['mc_id'], $lvl + 1);
        }
    }

    private function getFieldMap()
    {
        $map = array(
            'id' => 'mc_id',
            'title' => 'mc_title',
            'categoryid' => 'mc_cat_id',
            'ppid' => 'mc_parent_id',
            'showtitle' => 'mc_enable_title',
            'activefrom' => 'mc_start_date',
            'activeto' => 'mc_end_date',
            'active' => 'mc_status',
            'views' => 'mc_times_read',
            'data' => 'mc_text',
        );
        return $map;
    }

}