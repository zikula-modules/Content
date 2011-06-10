<?php

abstract class Content_Migration_AbstractModule
{
    /**
     * Should we migrate existing categories?
     * @var boolean
     */
    protected $migrateCategories = false;
    
    /**
     * Map of oldId => newIdPath
     * @var array
     */
    protected $categoryPathMap = array();
    
    /**
     * Map of oldId => newId
     * @var array
     */
    protected $categoryMap = array();
    
    /**
     * Where are the categories?
     * @var string
     */
    protected $categoryTable;
    
    /**
     * The 'local' category id of the root category in your table
     * @var integer
     */
    protected $rootCategoryLocalId = 0;

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
     * order the categories by parent id so lowest parent category ids are first, then by category id
     * e.g. ORDER BY pid, cid
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

    /**
     * migrate the categories provided
     */
    public function migrateCategories()
    {
        // create module category
        if (!CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Content')) {
            $this->createRootCategory();
        }

        $oldCategories = $this->getCategories();
        foreach ($oldCategories as $oldCategory) {
            if (isset($this->categoryPathMap[$oldCategory['pid']])) {
                $id = CategoryUtil::createCategory($this->categoryPathMap[$oldCategory['pid']], $oldCategory['title'], null, $oldCategory['title'], $oldCategory['title']);
                $this->categoryPathMap[$oldCategory['id']] = CategoryUtil::getCategoryById($id);
                $this->categoryMap[$oldCategory['id']] = $id;
            }

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
        $this->categoryPathMap[$this->rootCategoryLocalId] = CategoryUtil::getCategoryById($id);
        $this->categoryMap[$this->rootCategoryLocalId] = $id;
        return $id;
    }
}