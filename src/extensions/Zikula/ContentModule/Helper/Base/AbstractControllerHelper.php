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

namespace Zikula\ContentModule\Helper\Base;

use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\Bundle\CoreBundle\RouteUrl;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Helper base class for controller layer methods.
 */
abstract class AbstractControllerHelper
{
    use TranslatorTrait;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        PermissionHelper $permissionHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->permissionHelper = $permissionHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }
    
    /**
     * Returns an array of all allowed object types in ZikulaContentModule.
     *
     * @return string[] List of allowed object types
     */
    public function getObjectTypes(string $context = '', array $args = []): array
    {
        $allowedContexts = ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'mailz'];
        if (!in_array($context, $allowedContexts, true)) {
            $context = 'controllerAction';
        }
    
        $allowedObjectTypes = [];
        $allowedObjectTypes[] = 'page';
        $allowedObjectTypes[] = 'contentItem';
    
        return $allowedObjectTypes;
    }
    
    /**
     * Returns the default object type in ZikulaContentModule.
     */
    public function getDefaultObjectType(string $context = '', array $args = []): string
    {
        $allowedContexts = ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'mailz'];
        if (!in_array($context, $allowedContexts, true)) {
            $context = 'controllerAction';
        }
    
        return 'page';
    }
    
    /**
     * Processes the parameters for a view action.
     * This includes handling pagination, quick navigation forms and other aspects.
     */
    public function processViewActionParameters(
        string $objectType,
        SortableColumns $sortableColumns,
        array $templateParameters = [],
        bool $hasHookSubscriber = false
    ): array {
        $contextArgs = ['controller' => $objectType, 'action' => 'view'];
        if (!in_array($objectType, $this->getObjectTypes('controllerAction', $contextArgs), true)) {
            throw new Exception($this->trans('Error! Invalid object type received.'));
        }
    
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new Exception($this->trans('Error! Controller helper needs a request.'));
        }
        $repository = $this->entityFactory->getRepository($objectType);
    
        // parameter for used sorting field
        list($sort, $sortdir) = $this->determineDefaultViewSorting($objectType);
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = mb_strtolower($sortdir);
    
        if ('tree' === $request->query->getAlnum('tpl')) {
            $templateParameters['trees'] = $repository->selectAllTrees();
    
            return $this->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
        }
    
        $templateParameters['all'] = 'csv' === $request->getRequestFormat() ? 1 : $request->query->getInt('all');
        $showOnlyOwnEntriesSetting = (bool) $request->query->getInt(
            'own',
            (int) $this->variableApi->get('ZikulaContentModule', 'showOnlyOwnEntries')
        );
        $showOnlyOwnEntriesSetting = $showOnlyOwnEntriesSetting ? 1 : 0;
        $routeName = $request->get('_route');
        $isAdminArea = 'admin' === $templateParameters['routeArea'];
        if (!$isAdminArea && in_array($objectType, ['page'], true)) {
            $showOnlyOwnEntries = (bool) $this->variableApi->get('ZikulaContentModule', $objectType . 'PrivateMode', false);
            if (true === $showOnlyOwnEntries) {
                $templateParameters['own'] = 1;
            } else {
                $templateParameters['own'] = $showOnlyOwnEntriesSetting;
            }
        } else {
            $templateParameters['own'] = $showOnlyOwnEntriesSetting;
        }
    
        $resultsPerPage = 0;
        if (1 !== $templateParameters['all']) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $request->query->getInt('num');
            if (in_array($resultsPerPage, [0, 10], true)) {
                $resultsPerPage = $this->variableApi->get('ZikulaContentModule', $objectType . 'EntriesPerPage', 10);
            }
        }
        $templateParameters['num'] = $resultsPerPage;
        $templateParameters['tpl'] = $request->query->getAlnum('tpl');
    
        $templateParameters = $this->addTemplateParameters(
            $objectType,
            $templateParameters,
            'controllerAction',
            $contextArgs
        );
    
        $urlParameters = $templateParameters;
        foreach ($urlParameters as $parameterName => $parameterValue) {
            if (
                false === mb_stripos($parameterName, 'thumbRuntimeOptions')
                && false === mb_stripos($parameterName, 'featureActivationHelper')
                && false === mb_stripos($parameterName, 'permissionHelper')
            ) {
                continue;
            }
            unset($urlParameters[$parameterName]);
        }
    
        $quickNavFormType = 'Zikula\ContentModule\Form\Type\QuickNavigation\\'
            . ucfirst($objectType) . 'QuickNavType'
        ;
        $quickNavForm = $this->formFactory->create($quickNavFormType, $templateParameters);
        $quickNavForm->handleRequest($request);
        if ($quickNavForm->isSubmitted()) {
            $quickNavData = $quickNavForm->getData();
            foreach ($quickNavData as $fieldName => $fieldValue) {
                if ('routeArea' === $fieldName) {
                    continue;
                }
                if (in_array($fieldName, ['all', 'own', 'num'], true)) {
                    $templateParameters[$fieldName] = $fieldValue;
                    $urlParameters[$fieldName] = $fieldValue;
                } elseif ('sort' === $fieldName && !empty($fieldValue)) {
                    $sort = $fieldValue;
                } elseif ('sortdir' === $fieldName && !empty($fieldValue)) {
                    $sortdir = $fieldValue;
                } elseif (
                    false === mb_stripos($fieldName, 'thumbRuntimeOptions')
                    && false === mb_stripos($fieldName, 'featureActivationHelper')
                    && false === mb_stripos($fieldName, 'permissionHelper')
                ) {
                    // set filter as query argument, fetched inside CollectionFilterHelper
                    $request->query->set($fieldName, $fieldValue);
                    if ($fieldValue instanceof EntityAccess) {
                        $fieldValue = $fieldValue->getKey();
                    }
                    $urlParameters[$fieldName] = $fieldValue;
                }
            }
        }
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), mb_strtoupper($sortdir));
        $resultsPerPage = $templateParameters['num'];
        $request->query->set('own', $templateParameters['own']);
    
        $sortableColumns->setAdditionalUrlParameters($urlParameters);
        $useJoins = in_array($objectType, ['page']);
    
        $where = '';
        if (1 === $templateParameters['all']) {
            // retrieve item list without pagination
            $entities = $repository->selectWhere($where, $sort . ' ' . $sortdir, $useJoins);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $request->query->getInt('page', 1);
            $templateParameters['currentPage'] = $currentPage;
    
            // retrieve item list with pagination
            $paginator = $repository->selectWherePaginated(
                $where,
                $sort . ' ' . $sortdir,
                $currentPage,
                $resultsPerPage,
                $useJoins
            );
            $paginator->setRoute('zikulacontentmodule_' . mb_strtolower($objectType) . '_' . $templateParameters['routeArea'] . 'view');
            $paginator->setRouteParameters($urlParameters);
    
            $templateParameters['paginator'] = $paginator;
            $entities = $paginator->getResults();
        }
    
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['items'] = $entities;
    
        if (true === $hasHookSubscriber) {
            // build RouteUrl instance for display hooks
            $urlParameters['_locale'] = $request->getLocale();
            $routeName = 'zikulacontentmodule_' . mb_strtolower($objectType) . '_view';
            $templateParameters['currentUrlObject'] = new RouteUrl($routeName, $urlParameters);
        }
    
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['quickNavForm'] = $quickNavForm->createView();
    
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        // set current sorting in route parameters (e.g. for the pager)
        $routeParams = $request->attributes->get('_route_params');
        $routeParams['sort'] = $sort;
        $routeParams['sortdir'] = $sortdir;
        $request->attributes->set('_route_params', $routeParams);
    
        return $templateParameters;
    }
    
    /**
     * Determines the default sorting criteria.
     */
    protected function determineDefaultViewSorting(string $objectType): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return ['', 'ASC'];
        }
        $repository = $this->entityFactory->getRepository($objectType);
    
        $sort = $request->query->get('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields(), true)) {
            $sort = $repository->getDefaultSortingField();
            $request->query->set('sort', $sort);
            // set default sorting in route parameters (e.g. for the pager)
            $routeParams = $request->attributes->get('_route_params');
            $routeParams['sort'] = $sort;
            $request->attributes->set('_route_params', $routeParams);
        }
        $sortdir = $request->query->get('sortdir', 'ASC');
        if (false !== mb_strpos($sort, ' DESC')) {
            $sort = str_replace(' DESC', '', $sort);
            $sortdir = 'desc';
        }
    
        return [$sort, $sortdir];
    }
    
    /**
     * Processes the parameters for a display action.
     */
    public function processDisplayActionParameters(
        string $objectType,
        array $templateParameters = [],
        bool $hasHookSubscriber = false
    ): array {
        $contextArgs = ['controller' => $objectType, 'action' => 'display'];
        if (!in_array($objectType, $this->getObjectTypes('controllerAction', $contextArgs), true)) {
            throw new Exception($this->trans('Error! Invalid object type received.'));
        }
    
        if (true === $hasHookSubscriber) {
            // build RouteUrl instance for display hooks
            $entity = $templateParameters[$objectType];
            $urlParameters = $entity->createUrlArgs();
            $urlParameters['_locale'] = $this->requestStack->getCurrentRequest()->getLocale();
            $routeName = 'zikulacontentmodule_' . mb_strtolower($objectType) . '_display';
            $templateParameters['currentUrlObject'] = new RouteUrl($routeName, $urlParameters);
        }
    
        return $this->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
    }
    
    /**
     * Processes the parameters for an edit action.
     */
    public function processEditActionParameters(
        string $objectType,
        array $templateParameters = [],
        bool $hasHookSubscriber = false
    ): array {
        $contextArgs = ['controller' => $objectType, 'action' => 'edit'];
        if (!in_array($objectType, $this->getObjectTypes('controllerAction', $contextArgs), true)) {
            throw new Exception($this->trans('Error! Invalid object type received.'));
        }
    
        if (true === $hasHookSubscriber) {
            // build RouteUrl instance for display hooks
            $entity = $templateParameters[$objectType];
            $urlParameters = $entity->createUrlArgs();
            $urlParameters['_locale'] = $this->requestStack->getCurrentRequest()->getLocale();
            $routeName = 'zikulacontentmodule_' . mb_strtolower($objectType) . '_edit';
            $templateParameters['currentUrlObject'] = new RouteUrl($routeName, $urlParameters);
        }
    
        return $this->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
    }
    
    /**
     * Returns an array of additional template variables which are specific to the object type.
     */
    public function addTemplateParameters(
        string $objectType = '',
        array $parameters = [],
        string $context = '',
        array $args = []
    ): array {
        $allowedContexts = ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'mailz'];
        if (!in_array($context, $allowedContexts, true)) {
            $context = 'controllerAction';
        }
    
        if ('controllerAction' === $context) {
            if (!isset($args['action'])) {
                $routeName = $this->requestStack->getCurrentRequest()->get('_route');
                $routeNameParts = explode('_', $routeName);
                $args['action'] = end($routeNameParts);
            }
            if (in_array($args['action'], ['index', 'view'])) {
                $parameters = array_merge(
                    $parameters,
                    $this->collectionFilterHelper->getViewQuickNavParameters($objectType, $context, $args)
                );
            }
        }
        $parameters['permissionHelper'] = $this->permissionHelper;
    
        $parameters['featureActivationHelper'] = $this->featureActivationHelper;
    
        return $parameters;
    }
}
