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

namespace Zikula\ContentModule\Controller;

use Zikula\ContentModule\Controller\Base\AbstractPageController;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * Page controller class providing navigation and interaction functionality.
 */
class PageController extends AbstractPageController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/pages",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndexAction(Request $request)
    {
        return parent::adminIndexAction($request);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/pages",
     *        methods = {"GET"}
     * )
     */
    public function indexAction(Request $request)
    {
        // permission check
        $permLevel = ACCESS_READ;
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $this->redirectToRoute('zikulacontentmodule_page_sitemap');
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/admin/pages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::adminViewAction($request, $sort, $sortdir, $pos, $num);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/pages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::viewAction($request, $sort, $sortdir, $pos, $num);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/admin/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     * @Theme("admin")
     */
    public function adminEditAction(Request $request)
    {
        return parent::adminEditAction($request);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     */
    public function editAction(Request $request)
    {
        return parent::editAction($request);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminDisplayDeletedAction(Request $request, $id = 0)
    {
        return parent::adminLisplayDeletedAction($request, $id);
    }
    
    /**
     * @inheritDoc
     * @Route("/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function displayDeletedAction(Request $request, $id = 0)
    {
        return parent::displayDeletedAction($request, $id);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminLoggableHistoryAction(Request $request, $slug = '')
    {
        return parent::adminLoggableHistoryAction($request, $slug);
    }
    
    /**
     * @inheritDoc
     * @Route("/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function loggableHistoryAction(Request $request, $slug = '')
    {
        return parent::loggableHistoryAction($request, $slug);
    }
    
    /**
     * Handles management of content items for a given page.
     *
     * @Route("/admin/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("ZikulaContentModule:Page:manageContent.html.twig")
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function adminManageContentAction(Request $request, $slug)
    {
        return $this->manageContentInternal($request, $slug, true);
    }

    /**
     * Handles management of content items for a given page.
     *
     * @Route("/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("ZikulaContentModule:Page:manageContent.html.twig")
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function manageContentAction(Request $request, $slug)
    {
        return $this->manageContentInternal($request, $slug, false);
    }

    /**
     * This method includes the common implementation code for adminManageContentAction() and manageContentAction().
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    protected function manageContentInternal(Request $request, $slug, $isAdmin = false)
    {
        $page = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $this->get('router')->generate($routePrefix . 'view');
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer != $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        // try to guarantee that only one person at a time can be editing this entity
        $hasPageLockModule = $this->get('kernel')->isBundle('ZikulaPageLockModule');
        if (true === $hasPageLockModule) {
            $lockingApi = $this->get('zikula_pagelock_module.api.locking');
            $lockName = 'ZikulaContentModulePageContent' . $page->getKey();

            $lockingApi->addLock($lockName, $returnUrl);
        }

        $sectionStyles = $this->getVar('sectionStyles', '');
        $sectionStyleChoices = [];
        $userClasses = explode("\n", $sectionStyles);
        foreach ($userClasses as $class) {
            list($value, $text) = explode('|', $class);
            $value = trim($value);
            $text = trim($text);
            if (!empty($text) && !empty($value)) {
                $sectionStyleChoices[$text] = $value;
            }
        }

        return [
            'routeArea' => $routeArea,
            'page' => $page,
            'returnUrl' => $returnUrl,
            'sectionStyles' => $sectionStyleChoices
        ];
    }

    /**
     * Saves the layout data for a given page.
     *
     * @Route("/page/updateLayout/{id}",
     *        requirements = {"id" = "\d+"},
     *        defaults = {"id" = "0"},
     *        methods = {"POST"},
     *        options={"expose"=true}
     * )
     *
     * @param Request $request Current request instance
     * @param integer $id Identifier of treated page instance
     *
     * @return JsonResponse Output
     *
     * @throws NotFoundHttpException Thrown if the page was not found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function updateLayoutAction(Request $request, $id)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        $page = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectById($id);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $layoutData = $request->request->get('layoutData', []);
        $page->setLayout($layoutData);

        // no hook calls on purpose here, because layout data should not be of interest for other modules

        $workflowHelper = $this->get('zikula_content_module.workflow_helper');
        $success = $workflowHelper->executeAction($page, 'update');
        if (!$success) {
            return $this->json(['message' => $this->__('Error! An error occured during layout persistence.')], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => $this->__('Done! Layout saved.')]);
    }

    /**
     * Displays a sitemap.
     *
     * @Route("/sitemap.{_format}",
     *        requirements = {"_format" = "html|xml"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function sitemapAction(Request $request)
    {
        // permission check
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        $rootPages = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectWhere('tbl.lvl = 0');

        return $this->render('@ZikulaContentModule/Page/sitemap.' . $request->getRequestFormat() . '.twig', [
            'pages' => $rootPages
        ]);
    }
    
    /**
     * Handles duplication of a given page.
     *
     * @Route("/admin/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be duplicated isn't found
     */
    public function adminDuplicateAction(Request $request, $slug)
    {
        return $this->duplicateInternal($request, $slug, true);
    }

    /**
     * Handles duplication of a given page.
     *
     * @Route("/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be duplicated isn't found
     */
    public function duplicateAction(Request $request, $slug)
    {
        return $this->duplicateInternal($request, $slug, false);
    }

    /**
     * This method includes the common implementation code for adminDuplicateAction() and duplicateAction().
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be duplicated isn't found
     */
    protected function duplicateInternal(Request $request, $slug, $isAdmin = false)
    {
        $oldPage = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectBySlug($slug);
        if (null === $oldPage) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->mayEdit($oldPage)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $this->get('router')->generate($routePrefix . 'view');
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer != $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        $entityManager = $this->get('zikula_content_module.entity_factory')->getObjectManager();
        $titleSuffix = ' - ' . $this->__('new copy');
        $newPage = clone $oldPage;
        $newPage->setTitle($newPage->getTitle() . $titleSuffix);
        $slugParts = explode('/', $newPage->getSlug());
        $newPage->setSlug(end($slugParts) . str_replace(' ', '-', $titleSuffix));

        $hookHelper = $this->get('zikula_content_module.hook_helper');
        $workflowHelper = $this->get('zikula_content_module.workflow_helper');

        if ($newPage->supportsHookSubscribers()) {
            // Let any ui hooks perform additional validation actions
            $validationErrors = $hookHelper->callValidationHooks($newPage, UiHooksCategory::TYPE_VALIDATE_EDIT);
            if (count($validationErrors) > 0) {
                $this->addFlash('error', implode(' ', $validationErrors));

                return $this->redirect($returnUrl);
            }
        }

        $success = $workflowHelper->executeAction($newPage, 'submit');
        if (!$success) {
            $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

            return $this->redirect($returnUrl);
        }

        $modelHelper = $this->get('zikula_content_module.model_helper');
        $modelHelper->clonePageTranslations($oldPage->getId(), $newPage->getId(), $titleSuffix);

        $layoutData = $newPage->getLayout();
        foreach ($oldPage->getContentItems() as $item) {
            $newItem = clone $item;
            $newPage->addContentItems($newItem);

            if ($newItem->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $validationErrors = $hookHelper->callValidationHooks($newItem, UiHooksCategory::TYPE_VALIDATE_EDIT);
                if (count($validationErrors) > 0) {
                    $this->addFlash('error', implode(' ', $validationErrors));

                    continue;
                }
            }
            $success = $workflowHelper->executeAction($newItem, 'submit');
            if (!$success) {
                $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

                continue;
            }

            $modelHelper->cloneContentTranslations($item->getId(), $newItem->getId());

            if ($newItem->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated the content item
                $hookHelper->callProcessHooks($newItem, UiHooksCategory::TYPE_PROCESS_EDIT);
            }

            $oldItemId = $item->getId();
            $newItemId = $newItem->getId();
            foreach ($layoutData as $sectionKey => $section) {
                if (!isset($section['widgets']) || !is_array($section['widgets']) || !count($section['widgets'])) {
                    continue;
                }
                foreach ($section['widgets'] as $widgetKey => $widget) {
                    if ($widget['id'] != $oldItemId) {
                        continue;
                    }
                    $layoutData[$sectionKey]['widgets'][$widgetKey]['id'] = $newItemId;
                    break 2;
                }
            }
        }
        $newPage->setLayout($layoutData);

        $success = $workflowHelper->executeAction($newPage, 'update');
        if (!$success) {
            $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

            return $this->redirect($returnUrl);
        }

        $this->addFlash('success', $this->__('Done! Page duplicated.'));

        if ($newPage->supportsHookSubscribers()) {
            // Let any ui hooks know that we have updated the page
            $hookHelper->callProcessHooks($newPage, UiHooksCategory::TYPE_PROCESS_EDIT);
        }

        return $this->redirect($returnUrl);
    }
    
    /**
     * Displays sub pages of a given page.
     *
     * @Route("/subpages/{slug}.{_format}",
     *        requirements = {"slug" = "[^.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function subpagesAction(Request $request, $slug)
    {
        $page = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
        
        // permission check
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasEntityPermission($page, ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $this->get('zikula_content_module.view_helper')->processTemplate('page', 'subpages', [
            'page' => $page,
            'routeArea' => ''
        ]);
    }
    
    /**
     * @inheritDoc
     *
     * @Theme("admin")
     */
    public function adminDisplayAction(Request $request, $slug)
    {
        return parent::adminDisplayAction($request, $slug);
    }
    
    /**
     * @inheritDoc
     */
    public function displayAction(Request $request, $slug)
    {
        return parent::displayAction($request, $slug);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return parent::adminHandleSelectedEntriesAction($request);
    }
    
    /**
     * @inheritDoc
     * @Route("/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return parent::handleSelectedEntriesAction($request);
    }
}
