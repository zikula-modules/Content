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
    private $categoryPathMap = array();
    /**
     * Map of oldId => newId
     * @var array
     */
    private $categoryMap = array();
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
     * The 'local' page id of the root page in your table
     * @var integer
     */
    protected $rootPageLocalId = 0;
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
    protected $contentType = 'Html';
    /**
     * Which LayoutType to use?
     * $var string
     */
    protected $layoutType = 'Column1';
    /**
     * Map of oldPageId => newPageId
     * @var array
     */
    protected $pageMap = array();

    /**
     * Category data
     * structure return data in array(array('id' => '', 'pid' => '', 'title' => '', 'lang' => ''))
     * order the categories by parent id so lowest parent category ids are first, then by category id
     * e.g. ORDER BY pid, cid
     */
    abstract protected function getCategories();

    /**
     * Content data
     * structure return data as array of arrays by Content field names
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
     * public execute function
     */
    public function execute()
    {
        if ($this->migrateCategories) {
            $this->importCategories();
        }
        $this->importData();
    }

    /**
     * import the categories provided
     */
    private function importCategories()
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
        CategoryRegistryUtil::insertEntry('Content', 'content_page', 'Migrated', $id);
        $this->categoryPathMap[$this->rootCategoryLocalId] = CategoryUtil::getCategoryById($id);
        $this->categoryMap[$this->rootCategoryLocalId] = $id;
        return $id;
    }

    /**
     * create the new pages and content items from data
     */
    private function importData()
    {
        $pages = $this->getData();
        foreach ($pages as $page) {
            // create page 
            $page = array(
                'ppid' => $this->pageMap[$page['ppid']],
                'title' => $page['title'],
                'urlname' => DataUtil::formatForURL($page['title']),
                'layout' => $this->layoutType,
                'showtitle' => $page['showtitle'],
                'views' => $page['views'],
                'activefrom' => $page['activefrom'],
                'activeto' => $page['activeto'],
                'active' => $page['active'],
                'categoryid' => $this->categoryMap[$page['categoryid']],
                'setLeft' => '0',
                'setRight' => '1',
                'language' => ZLanguage::getLanguageCode());

            // insert the page
            $obj = DBUtil::insertObject($page, 'content_page');

            $this->pageMap[$page['id']] = $obj['id'];

            // create the contentitems for this page
            $content = array();
            $content[] = array(
                'pageId' => $obj['id'],
                'areaIndex' => '0',
                'position' => '0',
                'module' => 'Content',
                'type' => 'Heading',
                'data' => serialize(array(
                    'text' => $page['title'],
                    'headerSize' => 'h3')));
            $content[] = array(
                'pageId' => $obj['id'],
                'areaIndex' => '1',
                'position' => '0',
                'module' => 'Content',
                'type' => $this->contentType,
                'data' => serialize(array(
                    'text' => $page['data'],
                    'inputType' => 'html')));

            // write the items to the dbase
            foreach ($content as $contentitem) {
                DBUtil::insertObject($contentitem, 'content_content');
            }
        }
    }

}