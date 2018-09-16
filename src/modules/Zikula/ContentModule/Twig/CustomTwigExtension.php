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

namespace Zikula\ContentModule\Twig;

use Doctrine\DBAL\Driver\Connection;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\Collector\ContentTypeCollector;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Twig extension implementation class.
 */
class CustomTwigExtension extends Twig_Extension
{
    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ContentTypeCollector
     */
    protected $collector;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var boolean
     */
    protected $countPageViews;

    /**
     * @var boolean
     */
    protected $ignoreFirstTreeLevel;

    /**
     * CustomTwigExtension constructor.
     *
     * @param Connection                  $connection
     * @param RequestStack                $requestStack
     * @param Routerinterface             $router
     * @param ContentTypeCollector        $collector
     * @param PermissionHelper            $permissionHelper
     * @param ContentDisplayHelper        $displayHelper
     * @param CategoryHelper              $categoryHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param EntityFactory               $entityFactory
     * @param CollectionFilterHelper      $collectionFilterHelper
     * @param boolean                     $countPageViews
     * @param boolean                     $ignoreFirstTreeLevel
     */
    public function __construct(
        Connection $connection,
        RequestStack $requestStack,
        RouterInterface $router,
        ContentTypeCollector $collector,
        PermissionHelper $permissionHelper,
        ContentDisplayHelper $displayHelper,
        CategoryHelper $categoryHelper,
        CategoryRepositoryInterface $categoryRepository,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        $countPageViews = false,
        $ignoreFirstTreeLevel = true
    ) {
        $this->databaseConnection = $connection;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->collector = $collector;
        $this->permissionHelper = $permissionHelper;
        $this->displayHelper = $displayHelper;
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->countPageViews = $countPageViews;
        $this->ignoreFirstTreeLevel = $ignoreFirstTreeLevel;
    }

    /**
     * Returns a list of custom Twig functions.
     *
     * @return \Twig_SimpleFunction[] List of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('zikulacontentmodule_getPagePath', [$this, 'getPagePath'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('zikulacontentmodule_contentTypes', [$this, 'getContentTypes']),
            new \Twig_SimpleFunction('zikulacontentmodule_contentDetails', [$this, 'getContentDetails']),
            new \Twig_SimpleFunction('zikulacontentmodule_maySeeElement', [$this, 'isElementVisible']),
            new \Twig_SimpleFunction('zikulacontentmodule_categoryInfo', [$this, 'getCategoryInfo']),
            new \Twig_SimpleFunction('zikulacontentmodule_increaseAmountOfPageViews', [$this, 'increaseAmountOfPageViews']),
            new \Twig_SimpleFunction('zikulacontentmodule_hasReadAccess', [$this, 'hasReadAccess']),
            new \Twig_SimpleFunction('zikulacontentmodule_isCurrentPage', [$this, 'isCurrentPage']),
            new \Twig_SimpleFunction('zikulacontentmodule_getSlug', [$this, 'getPageSlug']),
            new \Twig_SimpleFunction('zikulacontentmodule_getPage', [$this, 'getPage'])
        ];
    }

    /**
     * The zikulacontentmodule_getPagePath function returns a breadcrumb style
     * list of pages.
     * Examples:
     *    {{ zikulacontentmodule_getPagePath(myPage) }}
     *
     * @param PageEntity|array $page
     * @param boolean $linkPages
     *
     * @return string
     */
    public function getPagePath($page, $linkPages = true)
    {
        $pages = [];

        $currentPage = $page;
        $pages[] = $currentPage;
        while (null !== $currentPage['parent']) {
            $currentPage = $currentPage['parent'];
            if (true !== $this->ignoreFirstTreeLevel || $currentPage->getLvl() > 0) {
                array_unshift($pages, $currentPage);
            }
        }

        $output = '<ol class="breadcrumb">';
        foreach ($pages as $aPage) {
            $content = $aPage['title'];
            if (true === $linkPages) {
                $link = $this->router->generate('zikulacontentmodule_page_display', ['slug' => $aPage['slug']]);
                $content = '<a href="' . $link . '" title="' . str_replace('"', '', $content) . '">' . $content . '</a>';
            }
            $output .= '<li' . ($aPage == $page ? ' class="active"' : '') . '>' . $content . '</li>';
        }
        $output .= '</ol>';

        return $output;
    }

    /**
     * The zikulacontentmodule_contentTypes function returns a list of all content types.
     * Examples:
     *    {% set contentTypes = zikulacontentmodule_contentTypes() %}     {# only active ones #}
     *    {% set contentTypes = zikulacontentmodule_contentTypes(true) %} {# also inactive ones #}
     *
     * @param boolean $includeInactive Whether also inactive content types should be returned or not (default false)
     *
     * @return array
     */
    public function getContentTypes($includeInactive = false)
    {
        if (true === $includeInactive) {
            return $this->collector->getAll();
        }

        return $this->collector->getActive();
    }

    /**
     * The zikulacontentmodule_contentDetails function returns all required details
     * for displaying content items of a given page.
     * Examples:
     *    {% set contentElements = zikulacontentmodule_contentDetails(page) %}
     *
     * @return array
     */
    public function getContentDetails(PageEntity $page)
    {
        $contentElements = [];
        foreach ($page->getContentItems() as $contentItem) {
            try {
                $contentElements[$contentItem->getId()] = $this->displayHelper->prepareForDisplay($contentItem, ContentTypeInterface::CONTEXT_VIEW);
            } catch (RuntimeException $exception) {
                $contentElements[$contentItem->getId()] = '';
            }
        }

        return $contentElements;
    }

    /**
     * The zikulacontentmodule_maySeeElement function checks whether a given
     * content item is visible for the current user or not.
     * Examples:
     *    {% if zikulacontentmodule_maySeeElement(contentItem) %}
     *
     * @return boolean
     */
    public function isElementVisible(ContentItemEntity $contentItem)
    {
        if (!$contentItem->getActive()) {
            return false;
        }
        $now = new \DateTime();
        if (null !== $contentItem->getActiveFrom() && $contentItem->getActiveFrom() > $now) {
            return false;
        }
        if (null !== $contentItem->getActiveTo() && $contentItem->getActiveTo() < $now) {
            return false;
        }
        if (!in_array($contentItem->getScope(), $this->collectionFilterHelper->getUserScopes())) {
            return false;
        }

        return true;
    }

    /**
     * The zikulacontentmodule_categoryInfo function returns all main categories
     * together with the amount of included pages.
     * Examples:
     *    {% set categoryInfoPerRegistry = zikulacontentmodule_categoryInfo() %}
     *
     * @return array
     */
    public function getCategoryInfo()
    {
        $properties = $this->categoryHelper->getAllPropertiesWithMainCat('page');
        if (!count($properties)) {
            return [];
        }

        $categoryInfoPerRegistry = [];
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $pageRepository = $this->entityFactory->getRepository('page');

        foreach ($properties as $categoryId) {
            $baseCategory = $this->categoryRepository->find($categoryId);
            if (null === $baseCategory) {
                continue;
            }
            $registryLabel = isset($baseCategory['display_name'][$locale]) ? $baseCategory['display_name'][$locale] : $baseCategory['display_name']['en'];

            $categories = $baseCategory->getChildren();
            $pageCounts = [];
            foreach ($categories as $category) {
                $qb = $pageRepository->getCountQuery('', false);
                $qb = $this->collectionFilterHelper->applyDefaultFilters('page', $qb);
                $qb->leftJoin('tbl.categories', 'tblCategories')
                   ->andWhere('tblCategories.category = :category')
                    ->setParameter('category', $category->getId());
                $pageCount = $qb->getQuery()->getSingleScalarResult();
                $pageCounts[$category->getId()] = $pageCount;
            }

            $categoryInfoPerRegistry[$registryLabel] = [
                'categories' => $categories,
                'pageCounts' => $pageCounts
            ];
        }

        return $categoryInfoPerRegistry;
    }

    /**
     * The zikulacontentmodule_increaseAmountOfPageViews function increases the view counter of a specific page.
     * It uses Doctrine DBAL to avoid creating a new page version.
     * Examples:
     *    {{ zikulacontentmodule_increaseAmountOfPageViews(page) }}
     *
     * @param PageEntity $page The given page instance
     */
    public function increaseAmountOfPageViews(PageEntity $page)
    {
        if (!$this->countPageViews) {
            return;
        }

        $pageId = $page->getId();

        // check against session to see if user was already counted
        $request = $this->requestStack->getCurrentRequest();
        $doCount = true;
        if (null !== $request) {
            if ($request->getSession()->has('ContentReadPage' . $pageId)) {
                $doCount = false;
            } else {
                $request->getSession()->set('ContentReadPage' . $pageId, 1);
            }
        }
        if (!$doCount) {
            return;
        }

        $views = $page->getViews() + 1;

        $this->databaseConnection->update('zikula_content_page', ['views' => $views], ['id' => $pageId]);
    }

    /**
     * The zikulacontentmodule_hasReadAccess function checks whether the currrent user
     * may read a certain entity or not.
     * Examples:
     *    {% if zikulacontentmodule_hasReadAccess(page) %}
     *
     * @param PageEntity|integer $page The given page instance or its identifier
     */
    public function hasReadAccess($page)
    {
        if (!($page instanceof PageEntity)) {
            $page = intval($page);
            if (!$page) {
                return false;
            }
            $page = $this->entityFactory->getRepository('page')->selectById($page, false);
            if (null === $page) {
                return false;
            }
        }

        return $this->permissionHelper->mayRead($page);
    }

    /**
     * The zikulacontentmodule_isCurrentPage function checks whether
     * the currrent page should be considered as active or not.
     * Examples:
     *    {% if zikulacontentmodule_isCurrentPage(page) %}
     *
     * @param PageEntity $page The given page instance
     *
     * @return boolean
     */
    public function isCurrentPage(PageEntity $page)
    {
        $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();
        $pagePath = $this->router->generate('zikulacontentmodule_page_display', ['slug' => $page->getSlug()]);
        if ($pagePath == $requestUri) {
            return true;
        }

        if (count($page->getChildren()) > 0) {
            foreach ($page->getChildren() as $subPage) {
                $isSubPageActive = $this->isCurrentPage($subPage);
                if ($isSubPageActive) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * The zikulacontentmodule_getSlug function returns the slug for a
     * given page identifier and the current locale.
     * Examples:
     *    <a href="{{ path('zikulacontentmodule_page_display', {slug: zikulacontentmodule_getSlug(2)}) }}" title="Test page">Test page</a>
     *
     * @param integer $pageId Page identifier
     *
     * @return string Page slug
     */
    public function getPageSlug($pageId)
    {
        $pageValues = $this->entityFactory->getRepository('page')->selectById($pageId, false, true);

        return isset($pageValues['slug']) ? $pageValues['slug'] : '';
    }

    /**
     * The zikulacontentmodule_getPage function returns a specific page for a
     * given page identifier or slug.
     * Examples:
     *    {% set page = zikulacontentmodule_getPage(123) %}
     *    {% set page = zikulacontentmodule_getPage('my/special/page) %}
     *
     * @param integer|string $pageId Page identifier or slug
     *
     * @return PageEntity Page instance
     */
    public function getPage($pageId)
    {
        if (is_numeric($pageId)) {
            return $this->entityFactory->getRepository('page')->selectById($pageId);
        }

        return $this->entityFactory->getRepository('page')->selectBySlug($pageId);
    }
}
