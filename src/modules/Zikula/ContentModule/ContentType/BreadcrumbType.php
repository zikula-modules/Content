<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

/**
 * Breadcrumb content type.
 */
class BreadcrumbType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'sitemap';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Breadcrumb');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Show breadcrumbs for hierarchical pages.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'includeSelf' => true, 
            'includeHome' => false,
            'translateTitles' => true, 
            'useGraphics' => false,
            'delimiter' => '&raquo;'
        ];
    }

/* TODO
    function display()
    {
        $path = [];
        $pageid = $this->getPageId();
        while ($pageid > 0) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
                        'id' => $pageid,
                        'includeContent' => false,
                        'includeLayout' => false,
                        'translate' => $this->translateTitles));
            if (!isset($this->includeSelf) || $this->includeSelf || $pageid != $this->getPageId()) {
                array_unshift($path, $page);
            }
            $pageid = $page['parentPageId'];
        }
        
        $this->view->assign('thispage', $this->getPageId());
        $this->view->assign('path', $path);
        $this->view->assign('useGraphics', $this->useGraphics);
        $this->view->assign('includeHome', $this->includeHome);
        $this->view->assign('delimiter', $this->delimiter);

        return $this->view->fetch($this->getTemplate());
    }

    function displayEditing()
    {
        return '';
    }
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}
