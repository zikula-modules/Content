<?php

class Content_Migration_ContentExpress extends Content_Migration_AbstractModule
{

    public function __construct()
    {
        $this->dataTable = 'ce_contentitems';
        $this->categoryTable = 'ce_categories';
        $this->migrateCategories = true;
        $this->rootCategoryLocalId = -1;
        $this->rootPageLocalId = -1;
        $this->pageMap[$this->rootPageLocalId] = 0;
        parent::__construct();
    }

    protected function getCategories()
    {
        $sql = "SELECT * FROM " . $this->tablePrefix . "_" . $this->categoryTable . " ORDER BY mc_parent_id, mc_id";
        $result = DBUtil::executeSQL();
        $categories = DBUtil::marshallFieldArray($result);
        $reformattedArray = array();
        foreach ($categories as $category) {
            $reformattedArray[] = array(
                'id' => $category['mc_id'],
                'pid' => $category['mc_parent_id'],
                'title' => $category['mc_title'],
            );
        }
        return $reformattedArray;
    }

    protected function getData()
    {
        $sql = "SELECT * FROM " . $this->tablePrefix . "_" . $this->dataTable . " ORDER BY mc_parent_id, mc_id";
        $result = DBUtil::executeSQL();
        $pages = DBUtil::marshallFieldArray($result);
        $reformattedArray = array();
        $fieldmap = $this->getFieldMap();
        $i = 0;
        foreach ($pages as $page) {
            foreach ($fieldmap as $newfield => $oldfield) {
                $reformattedArray[$i][$newfield] = $page[$oldfield];
            }
            $i++;
        }
        return $reformattedArray;
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