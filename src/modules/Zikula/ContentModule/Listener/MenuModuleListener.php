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

namespace Zikula\ContentModule\Listener;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\MenuModule\Event\ConfigureMenuEvent;

/**
 * Event subscriber for extending menus from the menu module.
 */
class MenuModuleListener implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * @var FactoryInterface.
     */
    protected $factory;

    /**
     * MenuModuleListener constructor.
     *
     * @param Routerinterface     $router
     * @param EntityFactory       $entityFactory
     * @param PermissionHelper    $permissionHelper
     * @param EntityDisplayHelper $entityDisplayHelper
     */
    public function __construct(
        RouterInterface $router,
        EntityFactory $entityFactory,
        PermissionHelper $permissionHelper,
        EntityDisplayHelper $entityDisplayHelper
    ) {
        $this->router = $router;
        $this->entityFactory = $entityFactory;
        $this->permissionHelper = $permissionHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
    }

    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigureMenuEvent::POST_CONFIGURE => ['extendMenu']
        ];
    }
    
    /**
     * Listener for the `zikulamenumodule.menu_post_configure` event.
     *
     * Occurs after a menu has been loaded.
     *
     * @param ConfigureMenuEvent $event The event instance
     */
    public function extendMenu(ConfigureMenuEvent $event)
    {
        $this->factory = $event->getFactory();
        $this->processItem($event->getMenu());
    }

    /**
     * Processes a menu item and it's children.
     *
     * @param ItemInterface $item
     */
    protected function processItem(ItemInterface $item)
    {
        $itemName = $item->getName();
        if (false !== stripos($itemName, 'ContentPages_')) {
            $pageId = intval(str_ireplace('ContentPages_', '', $itemName));
            $extras = $item->getExtras();
            $levels = isset($extras['levels']) ? intval($extras['levels']) : 0;
            if ($levels < 0) {
                $levels = 0;
            }
            $this->injectPages($item, $pageId, $levels);
        }
        foreach ($item->getChildren() as $subItem) {
            $this->processItem($subItem);
        }
    }

    /**
     * Injects a pages sub tree.
     *
     * @param ItemInterface $item
     * @param integer $pageId
     * @param integer $allowedLevels
     */
    protected function injectPages(ItemInterface $item, $pageId, $allowedLevels)
    {
        if ($pageId < 1) {
            $item->getParent()->removeChild($item);

            return;
        }
        $page = $this->entityFactory->getRepository('page')->selectById($pageId);
        if (null === $page || !$page->getInMenu() || !$this->permissionHelper->mayRead($page)) {
            $item->getParent()->removeChild($item);

            return;
        }

        // replace placeholder by root page of corresponding pages sub tree
        $title = $this->entityDisplayHelper->getFormattedTitle($page);
        $item->setName($title);
        $item->setUri($this->router->generate('zikulacontentmodule_page_display', $page->createUrlArgs()));
        $item->setLinkAttribute('title', $title);

        $this->addSubPages($item, $page, $allowedLevels, 1);
    }

    /**
     * Adds sub pages of a given page to a menu item.
     *
     * @param ItemInterface $menu
     * @param PageEntity $page
     * @param integer $allowedLevels
     * @param integer $currentLevel
     */
    protected function addSubPages(ItemInterface $menu, PageEntity $page, $allowedLevels, $currentLevel)
    {
        if (!count($page->getChildren())) {
            return;
        }
        if ($currentLevel >= $allowedLevels) {
            return;
        }
        foreach ($page->getChildren() as $subPage) {
            if (!$subPage->getInMenu() || !$this->permissionHelper->mayRead($subPage)) {
                continue;
            }
            $title = $this->entityDisplayHelper->getFormattedTitle($subPage);
            $item = $menu->addChild($title, [
                'route' => 'zikulacontentmodule_page_display',
                'routeParameters' => $subPage->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute('title', $title);
            $this->addSubPages($item, $subPage, $allowedLevels, $currentLevel + 1);
        }
    }
}
