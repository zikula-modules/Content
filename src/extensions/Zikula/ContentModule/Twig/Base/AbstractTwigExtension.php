<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Twig\Base;

use Doctrine\DBAL\Driver\Connection;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Renderer\ListRenderer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;
use Zikula\ContentModule\Menu\MenuBuilder;

/**
 * Twig extension base class.
 */
abstract class AbstractTwigExtension extends AbstractExtension
{
    use TranslatorTrait;
    
    /**
     * @var Connection
     */
    protected $databaseConnection;
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    /**
     * @var LoggableHelper
     */
    protected $loggableHelper;
    
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;
    
    public function __construct(
        TranslatorInterface $translator,
        Connection $connection,
        RouterInterface $router,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        WorkflowHelper $workflowHelper,
        ListEntriesHelper $listHelper,
        LoggableHelper $loggableHelper,
        MenuBuilder $menuBuilder
    ) {
        $this->setTranslator($translator);
        $this->databaseConnection = $connection;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->workflowHelper = $workflowHelper;
        $this->listHelper = $listHelper;
        $this->loggableHelper = $loggableHelper;
        $this->menuBuilder = $menuBuilder;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('zikulacontentmodule_treeData', [$this, 'getTreeData'], ['is_safe' => ['html']]),
            new TwigFunction('zikulacontentmodule_treeSelection', [$this, 'getTreeSelection']),
            new TwigFunction('zikulacontentmodule_increaseCounter', [$this, 'increaseCounter']),
            new TwigFunction('zikulacontentmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new TwigFunction('zikulacontentmodule_templateSelector', [$this, 'getTemplateSelector']),
        ];
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('zikulacontentmodule_listEntry', [$this, 'getListEntry']),
            new TwigFilter('zikulacontentmodule_logDescription', [$this, 'getLogDescription']),
            new TwigFilter('zikulacontentmodule_formattedTitle', [$this, 'getFormattedEntityTitle']),
            new TwigFilter('zikulacontentmodule_objectState', [$this, 'getObjectState'], ['is_safe' => ['html']]),
        ];
    }
    
    public function getTests()
    {
        return [
            new TwigTest('zikulacontentmodule_instanceOf', static function ($var, $instance) {
                return $var instanceof $instance;
            }),
        ];
    }
    
    /**
     * The zikulacontentmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|zikulacontentmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|zikulacontentmodule_objectState(false) }} {# no ui feedback #}.
     */
    public function getObjectState(string $state = 'initial', bool $uiFeedback = true): string
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if (true === $uiFeedback) {
            $result = '<span class="badge badge-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    /**
     * The zikulacontentmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|zikulacontentmodule_listEntry('entityName', 'fieldName') }}.
     */
    public function getListEntry(
        string $value,
        string $objectType = '',
        string $fieldName = '',
        string $delimiter = ', '
    ): string {
        if ((empty($value) && '0' !== $value) || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    /**
     * The zikulacontentmodule_treeData function delivers the html output for a JS tree
     * based on given tree entities.
     */
    public function getTreeData(string $objectType, array $tree = [], string $routeArea = '', int $rootId = 1): array
    {
        // check whether an edit action is available
        $hasEditAction = in_array($objectType, ['page'], true);
    
        $repository = $this->entityFactory->getRepository($objectType);
        $descriptionFieldName = $this->entityDisplayHelper->getDescriptionFieldName($objectType);
    
        $result = [
            'nodes' => '',
            'actions' => '',
        ];
        foreach ($tree as $node) {
            if (1 > $node->getLvl() || $rootId === $node->getKey()) {
                list($nodes, $actions) = $this->processTreeItemWithChildren(
                    $objectType,
                    $node,
                    $routeArea,
                    $rootId,
                    $descriptionFieldName,
                    $hasEditAction
                );
                $result['nodes'] .= $nodes;
                $result['actions'] .= $actions;
            }
        }
    
        return $result;
    }
    
    /**
     * Builds an unordered list for a tree node and it's children.
     */
    protected function processTreeItemWithChildren(
        string $objectType,
        EntityAccess $node,
        string $routeArea,
        int $rootId,
        string $descriptionFieldName,
        bool $hasEditAction
    ): array {
        $idPrefix = 'tree' . $rootId . 'node_' . $node->getKey();
        $title = '' !== $descriptionFieldName ? strip_tags($node[$descriptionFieldName]) : '';
    
        $needsArg = in_array($objectType, ['page'], true);
        $urlArgs = $needsArg ? $node->createUrlArgs(true) : $node->createUrlArgs();
        $urlDataAttributes = '';
        foreach ($urlArgs as $field => $value) {
            $urlDataAttributes .= ' data-' . $field . '="' . $value . '"';
        }
    
        $titleAttribute = ' title="' . str_replace('"', '', $title) . '"';
        $classAttribute = ' class="lvl' . $node->getLvl() . '"';
        $liTag = '<li id="' . $idPrefix . '"' . $titleAttribute . $classAttribute . $urlDataAttributes . '>';
        $liContent = $this->entityDisplayHelper->getFormattedTitle($node);
        if ($hasEditAction) {
            $routeName = 'zikulacontentmodule_' . mb_strtolower($objectType) . '_' . $routeArea . 'edit';
            $url = $this->router->generate($routeName, $urlArgs);
            $liContent = '<a href="' . $url . '" title="' . str_replace('"', '', $title) . '">' . $liContent . '</a>';
        }
    
        $nodeItem = $liTag . $liContent;
    
        $itemActionsMenu = $this->menuBuilder->createItemActionsMenu([
            'entity' => $node,
            'area' => $routeArea,
            'context' => 'view',
        ]);
        $renderer = new ListRenderer(new Matcher());
    
        $actions = '<li id="itemActions' . $node->getKey() . '">';
        $actions .= $renderer->render($itemActionsMenu);
        $actions = str_replace([' class="first"', ' class="last"'], '', $actions);
        $actions .= '</li>';
    
        if (0 < count($node->getChildren())) {
            $nodeItem .= '<ul>';
            foreach ($node->getChildren() as $childNode) {
                list($subNodes, $subActions) = $this->processTreeItemWithChildren(
                    $objectType,
                    $childNode,
                    $routeArea,
                    $rootId,
                    $descriptionFieldName,
                    $hasEditAction
                );
                $nodeItem .= $subNodes;
                $actions .= $subActions;
            }
            $nodeItem .= '</ul>';
        }
    
        $nodeItem .= '</li>';
    
        return [$nodeItem, $actions];
    }
    
    /**
     * The zikulacontentmodule_treeSelection function retrieves tree entities based on a given one.
     */
    public function getTreeSelection(
        string $objectType,
        EntityAccess $node,
        string $target,
        bool $skipRootNode = true
    ): ?array {
        $repository = $this->entityFactory->getRepository($objectType);
        $titleFieldName = $this->entityDisplayHelper->getTitleFieldName($objectType);
    
        $result = null;
    
        switch ($target) {
            case 'allParents':
            case 'directParent':
                $path = $repository->getPath($node);
                if (0 < count($path)) {
                    // remove $node
                    unset($path[count($path) - 1]);
                }
                if ($skipRootNode && 0 < count($path)) {
                    // remove root level
                    array_shift($path);
                }
                if ('allParents' === $target) {
                    $result = $path;
                } elseif ('directParent' === $target && 0 < count($path)) {
                    $result = $path[count($path) - 1];
                }
                break;
            case 'allChildren':
            case 'directChildren':
                $direct = 'directChildren' === $target;
                $sortByField = '' !== $titleFieldName ? $titleFieldName : null;
                $sortDirection = 'ASC';
                $result = $repository->children($node, $direct, $sortByField, $sortDirection);
                break;
            case 'predecessors':
                $includeSelf = false;
                $result = $repository->getPrevSiblings($node, $includeSelf);
                break;
            case 'successors':
                $includeSelf = false;
                $result = $repository->getNextSiblings($node, $includeSelf);
                break;
            case 'preandsuccessors':
                $includeSelf = false;
                $result = array_merge(
                    $repository->getPrevSiblings($node, $includeSelf),
                    $repository->getNextSiblings($node, $includeSelf)
                );
                break;
        }
    
        return $result;
    }
    
    /**
     * The zikulacontentmodule_increaseCounter function increases a counter field of a specific entity.
     * It uses Doctrine DBAL to avoid creating a new loggable version, sending workflow notification or executing other unwanted actions.
     * Example:
     *     {{ zikulacontentmodule_increaseCounter(page, 'views') }}.
     */
    public function increaseCounter(EntityAccess $entity, string $fieldName = ''): void
    {
        $entityId = $entity->getId();
        $objectType = $entity->get_objectType();
    
        // check against session to see if user was already counted
        $request = $this->requestStack->getCurrentRequest();
        $doCount = true;
        if (null !== $request && $request->hasSession() && $session = $request->getSession()) {
            if ($session->has('ZikulaContentModuleRead' . $objectType . $entityId)) {
                $doCount = false;
            } else {
                $session->set('ZikulaContentModuleRead' . $objectType . $entityId, 1);
            }
        }
        if (!$doCount) {
            return;
        }
    
        $counterValue = $entity[$fieldName] + 1;
    
        $this->databaseConnection->update(
            'zikula_content_' . mb_strtolower($objectType),
            [$fieldName => $counterValue],
            ['id' => $entityId]
        );
    }
    
    
    
    
    /**
     * The zikulacontentmodule_formattedTitle filter outputs a formatted title for a given entity.
     * Example:
     *     {{ myPost|zikulacontentmodule_formattedTitle }}.
     */
    public function getFormattedEntityTitle(EntityAccess $entity): string
    {
        return $this->entityDisplayHelper->getFormattedTitle($entity);
    }
    
    /**
     * The zikulacontentmodule_logDescription filter returns the translated clear text
     * description for a given log entry.
     * Example:
     *     {{ logEntry|zikulacontentmodule_logDescription }}.
     */
    public function getLogDescription(AbstractLogEntry $logEntry): string
    {
        return $this->loggableHelper->translateActionDescription($logEntry);
    }
}
