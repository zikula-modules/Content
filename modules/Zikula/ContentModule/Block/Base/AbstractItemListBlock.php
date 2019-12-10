<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Block\Base;

use Exception;
use Twig\Loader\FilesystemLoader;
use Zikula\BlocksModule\AbstractBlockHandler;
use Zikula\ContentModule\Block\Form\Type\ItemListBlockType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;
use Zikula\ContentModule\Helper\ModelHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Generic item list block base class.
 */
abstract class AbstractItemListBlock extends AbstractBlockHandler
{
    /**
     * @var FilesystemLoader
     */
    protected $twigLoader;
    
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var ModelHelper
     */
    protected $modelHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var categoryHelper
     */
    protected $categoryHelper;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    /**
     * List of object types allowing categorisation.
     *
     * @var array
     */
    protected $categorisableObjectTypes;
    
    public function getType(): string
    {
        return $this->__('Content list', 'zikulacontentmodule');
    }
    
    public function display(array $properties = []): string
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('ZikulaContentModule:ItemListBlock:', $properties['title'] . '::', ACCESS_OVERVIEW)) {
            return '';
        }
    
        $this->categorisableObjectTypes = ['page'];
    
        // set default values for all params which are not properly set
        $defaults = $this->getDefaults();
        $properties = array_merge($defaults, $properties);
    
        $contextArgs = ['name' => 'list'];
        if (!isset($properties['objectType']) || !in_array($properties['objectType'], $this->controllerHelper->getObjectTypes('block', $contextArgs), true)) {
            $properties['objectType'] = $this->controllerHelper->getDefaultObjectType('block', $contextArgs);
        }
    
        $objectType = $properties['objectType'];
    
        $hasCategories = in_array($objectType, $this->categorisableObjectTypes, true)
            && $this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $properties['objectType']);
        if ($hasCategories) {
            $categoryProperties = $this->resolveCategoryIds($properties);
        }
    
        $repository = $this->entityFactory->getRepository($objectType);
    
        // create query
        $orderBy = $this->modelHelper->resolveSortParameter($objectType, $properties['sorting']);
        $qb = $repository->getListQueryBuilder($properties['filter'], $orderBy);
    
        if ($hasCategories) {
            // apply category filters
            if (is_array($properties['categories']) && count($properties['categories']) > 0) {
                $qb = $this->categoryHelper->buildFilterClauses($qb, $objectType, $properties['categories']);
            }
        }
    
        // get objects from database
        $currentPage = 1;
        $resultsPerPage = $properties['amount'];
        $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        try {
            list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
        } catch (Exception $exception) {
            $entities = [];
            $objectCount = 0;
        }
    
        // filter by permissions
        $entities = $this->permissionHelper->filterCollection($objectType, $entities, ACCESS_READ);
    
        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('Content list', 'zikulacontentmodule');
        }
    
        $template = $this->getDisplayTemplate($properties);
    
        $templateParameters = [
            'vars' => $properties,
            'objectType' => $objectType,
            'items' => $entities
        ];
        if ($hasCategories) {
            $templateParameters['properties'] = $categoryProperties;
        }
    
        $templateParameters = $this->controllerHelper->addTemplateParameters($properties['objectType'], $templateParameters, 'block');
    
        return $this->renderView($template, $templateParameters);
    }
    
    /**
     * Returns the template used for output.
     */
    protected function getDisplayTemplate(array $properties = []): string
    {
        $templateFile = $properties['template'];
        if ('custom' === $templateFile && null !== $properties['customTemplate'] && '' !== $properties['customTemplate']) {
            $templateFile = $properties['customTemplate'];
        }
    
        $templateForObjectType = str_replace('itemlist_', 'itemlist_' . $properties['objectType'] . '_', $templateFile);
    
        $templateOptions = [
            'Block/' . $templateForObjectType,
            'Block/' . $templateFile,
            'Block/itemlist.html.twig'
        ];
    
        $template = '';
        foreach ($templateOptions as $templatePath) {
            if ($this->twigLoader->exists('@ZikulaContentModule/' . $templatePath)) {
                $template = '@ZikulaContentModule/' . $templatePath;
                break;
            }
        }
    
        return $template;
    }
    
    public function getFormClassName(): string
    {
        return ItemListBlockType::class;
    }
    
    public function getFormOptions(): array
    {
        $objectType = 'page';
        $this->categorisableObjectTypes = ['page'];
    
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request && $request->attributes->has('blockEntity')) {
            $blockEntity = $request->attributes->get('blockEntity');
            if (is_object($blockEntity) && method_exists($blockEntity, 'getProperties')) {
                $blockProperties = $blockEntity->getProperties();
                if (isset($blockProperties['objectType'])) {
                    $objectType = $blockProperties['objectType'];
                } else {
                    // set default options for new block creation
                    $blockEntity->setProperties($this->getDefaults());
                }
            }
        }
    
        return [
            'object_type' => $objectType,
            'is_categorisable' => in_array($objectType, $this->categorisableObjectTypes, true),
            'category_helper' => $this->categoryHelper,
            'feature_activation_helper' => $this->featureActivationHelper
        ];
    }
    
    public function getFormTemplate(): string
    {
        return '@ZikulaContentModule/Block/itemlist_modify.html.twig';
    }
    
    /**
     * Returns default settings for this block.
     */
    protected function getDefaults(): array
    {
        return [
            'objectType' => 'page',
            'sorting' => 'default',
            'amount' => 5,
            'template' => 'itemlist_display.html.twig',
            'customTemplate' => null,
            'filter' => ''
        ];
    }
    
    /**
     * Resolves category filter ids.
     */
    protected function resolveCategoryIds(array $properties = []): array
    {
        $primaryRegistry = $this->categoryHelper->getPrimaryProperty($properties['objectType']);
        if (!isset($properties['categories'])) {
            $properties['categories'] = [$primaryRegistry => []];
        } else {
            if (!is_array($properties['categories'])) {
                $properties['categories'] = explode(',', $properties['categories']);
            }
            if (count($properties['categories']) > 0) {
                $firstCategories = reset($properties['categories']);
                if (!is_array($firstCategories)) {
                    $firstCategories = [$firstCategories];
                }
                $properties['categories'] = [$primaryRegistry => $firstCategories];
            }
        }
    
        return $properties;
    }
    
    /**
     * @required
     */
    public function setTwigLoader(FilesystemLoader $twigLoader): void
    {
        $this->twigLoader = $twigLoader;
    }
    
    /**
     * @required
     */
    public function setControllerHelper(ControllerHelper $controllerHelper): void
    {
        $this->controllerHelper = $controllerHelper;
    }
    
    /**
     * @required
     */
    public function setModelHelper(ModelHelper $modelHelper): void
    {
        $this->modelHelper = $modelHelper;
    }
    
    /**
     * @required
     */
    public function setPermissionHelper(PermissionHelper $permissionHelper): void
    {
        $this->permissionHelper = $permissionHelper;
    }
    
    /**
     * @required
     */
    public function setEntityFactory(EntityFactory $entityFactory): void
    {
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * @required
     */
    public function setCategoryDependencies(
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper
    ): void {
        $this->categoryHelper = $categoryHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }
}
