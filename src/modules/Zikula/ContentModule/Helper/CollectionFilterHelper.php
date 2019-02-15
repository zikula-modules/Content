<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper;

use Doctrine\ORM\QueryBuilder;
use Zikula\ContentModule\Helper\Base\AbstractCollectionFilterHelper;

/**
 * Entity collection filter helper implementation class.
 */
class CollectionFilterHelper extends AbstractCollectionFilterHelper
{
    /**
     * @var boolean
     */
    protected $ignoreFirstTreeLevel;

    /**
     * @inheritDoc
     */
    protected function applyDefaultFiltersForPage(QueryBuilder $qb, array $parameters = [])
    {
        $qb = parent::applyDefaultFiltersForPage($qb, $parameters);
        if (true === $this->skipDefaultFilters()) {
            return $qb;
        }

        $qb->andWhere('tbl.active = 1');
        if (true === $this->ignoreFirstTreeLevel) {
            $qb->andWhere('tbl.lvl > 0');
        }
        if (in_array('tblContentItems', $qb->getAllAliases())) {
            $routeName = $this->requestStack->getCurrentRequest()->get('_route');
            if (!in_array($routeName, ['zikulacontentmodule_page_display', 'zikulacontentmodule_external_finder'])) {
                $qb->andWhere('tblContentItems.active = 1');
                $qb = $this->applyDateRangeFilterForContentItem($qb, 'tblContentItems');
            }
        }

        return $qb;
    }

    /**
     * @inheritDoc
     */
    protected function applyDefaultFiltersForContentItem(QueryBuilder $qb, array $parameters = [])
    {
        $qb = parent::applyDefaultFiltersForContentItem($qb, $parameters);
        if (true === $this->skipDefaultFilters()) {
            return $qb;
        }

        if ($this->requestStack->getCurrentRequest()->getSession()->has('ContentAllowInactiveElements')) {
            return $qb;
        }

        $qb->andWhere('tbl.active = 1');
        if (in_array('tblPage', $qb->getAllAliases())) {
            $qb->andWhere('tblPage.active = 1');
            $qb = $this->applyDateRangeFilterForPage($qb, 'tblPage');
        }

        return $qb;
    }

    /**
     * Checks if default filters should be skipped for the current request.
     *
     * @return boolean
     */
    protected function skipDefaultFilters()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return true;
        }
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_page_admin')
            || 'zikulacontentmodule_page_edit' == $routeName
            || false !== strpos($routeName, 'zikulacontentmodule_contentitem_admin')
        ;
        if ($isAdminArea/* || $this->permissionHelper->hasComponentPermission('page', ACCESS_ADD)*/) {
            return true;
        }
        if (1 == $request->query->getInt('preview', 0) && $this->permissionHelper->hasComponentPermission('page', ACCESS_ADMIN)) {
            return true;
        }
        if (in_array($routeName, [
            'zikulacontentmodule_page_managecontent',
            'zikulacontentmodule_contentitem_displayediting',
            'zikulacontentmodule_page_sitemap'
        ])) {
            return true;
        }

        return false;
    }

    /**
     * @param boolean $ignoreFirstTreeLevel
     */
    public function setIgnoreFirstTreeLevel($ignoreFirstTreeLevel = true)
    {
        $this->ignoreFirstTreeLevel = $ignoreFirstTreeLevel;
    }
}
