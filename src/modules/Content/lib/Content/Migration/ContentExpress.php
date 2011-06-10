<?php

class Content_Migration_ContentExpress extends Content_Migration_AbstractModule
{
    public function __construct()
    {
        $this->dataTable = 'ce_contentitems';
        $this->categoryTable = 'ce_categories';
        $this->contentType = 'Html';
        parent::__construct();
    }
    
//    public function getFields()
//    {
//        $fields = array(
//            'mc_id',
//            'mc_title',
//            'mc_text',
//            'mc_cat_id',
//            'mc_parent_id',
//            'mc_enable_title',
//            'mc_start_date',
//            'mc_end_date',
//            'mc_status',
//            'mc_author',
//            'mc_times_read',
//        );
//        return $fields;
//    }
    
    public function getFieldMap()
    {
        $map = array(
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
    
    public function getCategories()
    {
        $sql = "SELECT * FROM " . $this->tablePrefix . "_" . $this->categoryTable;
        $result = DBUtil::executeSQL();
        $categories = DBUtil::marshallFieldArray($result, true, 'mc_id');
        $reformattedArray = array();
        foreach ($categories as $category) {
            $reformattedArray[] = array(
                'id' => $category['mc_id'],
                'pid' => $category['mc_parent_id'],
                'title' => $category['mc_title'],
                'lang' => ZLanguage::translateLegacyCode($category['mc_language']),
            );
        }
        return $reformattedArray;
    }
    
    public function getData()
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

}