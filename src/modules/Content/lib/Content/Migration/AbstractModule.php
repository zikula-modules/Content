<?php

abstract class Content_Migration_AbstractModule
{
    /**
     * Should we migrate existing categories?
     * @var boolean
     */
    protected $migrateCategories = false;
    
    /**
     * Map of oldId => newID
     * @var array
     */
    protected $categoryMap = array();
    /**
     * Zikula's current table prefix
     * @var string
     */
    protected $tablePrefix;
    
    /**
     * Where is the data stored
     * @var string
     */
    protected $dataTable;
    
    /**
     * Where are the categories?
     * @var string
     */
    protected $categoryTable;
    
    /**
     * Which ContentType to use?
     * $var string
     */
    protected $contentType;
    
    /**
     * existing fields (columns) in module
     */
//    abstract protected function getFields();

    /**
     * map target module fields to Content module fields
     */
    abstract protected function getFieldMap();
    
    /**
     * Category data
     * structure return data in array(array('id' => '', 'pid' => '', 'title' => '', 'lang' => ''))
     */
    abstract protected function getCategories();

    /**
     * Content data
     * structure return data by Content field names in getFieldMap()
     */
    abstract protected function getData();

    /**
     * constructor
     */
    public function __construct()
    {
        $this->tablePrefix = System::getVar('prefix');
    }
    
    public function migrateCategories()
    {
        // create module category
        $rootCat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Content');
        if (!$rootCat) {
            $rootId = $rootCat['id'];
        } else {
            $rootId = $this->createRootCategory();
        }
        
        $oldCategories = $this->getCategories();
        foreach ($oldCategories as $oldCategory) {
            CategoryUtil::createCategory('/__SYSTEM__/Modules/Content', $oldCategory['title'], null, $oldCategory['title'], $oldCategory['title']);

        }
    }

    /**
     * create the default category tree
     * @return boolean
     */
    private function createRootCategory()
    {
        $id = CategoryUtil::createCategory('/__SYSTEM__/Modules', 'Content', null, $this->__('Content'), $this->__('Migrated Content categories'));
        // create an entry in the categories registry to the property
        CategoryRegistryUtil::insertEntry ('Content', 'content_page', 'Migrated', $id);
        return $id;
    }
}