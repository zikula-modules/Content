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
     * array of records to be converted
     * @var array
     */
    protected $records = array();

    /**
     * Category data
     * structure return data in array(array('id' => '', 'pid' => '', 'title' => '', 'lang' => ''))
     * order the categories by parent id so lowest parent category ids are first, then by category id
     * e.g. ORDER BY pid, cid
     */
    abstract protected function getCategories();

    /**
     * Content records
     * structure return data as array of arrays by Content field names
     */
    abstract protected function createRecords();

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
        $rootCatId = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', ModUtil::getVar('Content', 'categoryPropPrimary'));
        $rootCatObj = CategoryUtil::getCategoryById($rootCatId);
        $this->categoryPathMap[$this->rootCategoryLocalId] = $rootCatObj['path'];
        $this->categoryMap[$this->rootCategoryLocalId] = (int)$rootCatId;

        $oldCategories = $this->getCategories();
        foreach ($oldCategories as $oldCategory) {
            if (isset($this->categoryPathMap[$oldCategory['pid']])) {
                $id = CategoryUtil::createCategory($this->categoryPathMap[$oldCategory['pid']], $oldCategory['title'], null, $oldCategory['title'], $oldCategory['title']);
                $catObj = CategoryUtil::getCategoryById($id);
                $this->categoryPathMap[$oldCategory['id']] = $catObj['path'];
                $this->categoryMap[$oldCategory['id']] = (int)$id;
            }
        }
    }

    /**
     * create the new pages and content items from data
     */
    private function importData()
    {
        $this->createRecords();
        $items = $this->records;
//        echo "<pre>";
//        var_dump($this->pageMap);
//        echo "------------<br />";
//        var_dump($this->categoryMap);
//        echo "------------<br />";
//        $i = 100;
        foreach ($items as $item) {
//            var_dump($item);
            // create page 
            $page = array(
                'parentPageId' => (int)$this->pageMap[(int)$item['ppid']],
                'level' => $item['level'],
                'title' => $item['title'],
                'urlname' => DataUtil::formatForURL($item['title']),
                'layout' => $this->layoutType,
                'showTitle' => $item['showtitle'],
                'views' => $item['views'],
                'activeFrom' => $item['activefrom'],
                'activeTo' => $item['activeto'],
                'active' => $item['active'],
                'categoryId' => $this->categoryMap[(int)$item['categoryid']],
                'position' => $item['position'],
                'setLeft' => '0',
                'setRight' => '1',
                'language' => ZLanguage::getLanguageCode());
//            var_dump($page);

            // insert the page
            $obj = DBUtil::insertObject($page, 'content_page');

            $this->pageMap[(int)$item['id']] = (int)$obj['id'];

            // create the contentitems for this page
            $content = array();
            $content[] = array(
                'pageId' => $obj['id'],
                'areaIndex' => '0',
                'position' => '0',
                'module' => 'Content',
                'type' => 'Heading',
                'data' => serialize(array(
                    'text' => $item['title'],
                    'headerSize' => 'h3')));
            $content[] = array(
                'pageId' => $obj['id'],
                'areaIndex' => '1',
                'position' => '0',
                'module' => 'Content',
                'type' => $this->contentType,
                'data' => serialize(array(
                    'text' => $item['data'],
                    'inputType' => 'text')));

            // write the items to the dbase
            foreach ($content as $contentitem) {
                DBUtil::insertObject($contentitem, 'content_content');
            }
//            $i++;
//            if ($i == 110) {
//                var_dump($this->pageMap); die;
//            }
        }
    }

}