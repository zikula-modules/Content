<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Composite;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\RouteUrl;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\SearchModule\Entity\SearchResultEntity;
use Zikula\SearchModule\SearchableInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Search helper base class.
 */
abstract class AbstractSearchHelper implements SearchableInterface
{
    use TranslatorTrait;
    
    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;
    
    /**
     * @var SessionInterface
     */
    protected $session;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    /**
     * SearchHelper constructor.
     *
     * @param TranslatorInterface $translator          Translator service instance
     * @param PermissionApiInterface $permissionApi    PermissionApi service instance
     * @param SessionInterface    $session             Session service instance
     * @param RequestStack        $requestStack        RequestStack service instance
     * @param EntityFactory       $entityFactory       EntityFactory service instance
     * @param ControllerHelper    $controllerHelper    ControllerHelper service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param FeatureActivationHelper $featureActivationHelper FeatureActivationHelper service instance
     * @param CategoryHelper      $categoryHelper      CategoryHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        PermissionApiInterface $permissionApi,
        SessionInterface $session,
        RequestStack $requestStack,
        EntityFactory $entityFactory,
        ControllerHelper $controllerHelper,
        EntityDisplayHelper $entityDisplayHelper,
        FeatureActivationHelper $featureActivationHelper,
        CategoryHelper $categoryHelper
    ) {
        $this->setTranslator($translator);
        $this->permissionApi = $permissionApi;
        $this->session = $session;
        $this->request = $requestStack->getCurrentRequest();
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->featureActivationHelper = $featureActivationHelper;
        $this->categoryHelper = $categoryHelper;
    }
    
    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * @inheritDoc
     */
    public function amendForm(FormBuilderInterface $builder)
    {
        if (!$this->permissionApi->hasPermission('ZikulaContentModule::', '::', ACCESS_READ)) {
            return '';
        }
    
        $builder->add('active', HiddenType::class, [
            'data' => true
        ]);
    
        $searchTypes = $this->getSearchTypes();
    
        foreach ($searchTypes as $searchType => $typeInfo) {
            $builder->add('active_' . $searchType, CheckboxType::class, [
                'value' => $typeInfo['value'],
                'label' => $typeInfo['label'],
                'label_attr' => ['class' => 'checkbox-inline'],
                'required' => false
            ]);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function getResults(array $words, $searchType = 'AND', $modVars = null)
    {
        if (!$this->permissionApi->hasPermission('ZikulaContentModule::', '::', ACCESS_READ)) {
            return [];
        }
    
        // initialise array for results
        $results = [];
    
        // retrieve list of activated object types
        $searchTypes = $this->getSearchTypes();
        $entitiesWithDisplayAction = ['page'];
    
        foreach ($searchTypes as $searchTypeCode => $typeInfo) {
            $isActivated = false;
            $searchSettings = $this->request->query->get('zikulasearchmodule_search', []);
            $moduleActivationInfo = $searchSettings['modules'];
            if (isset($moduleActivationInfo['ZikulaContentModule'])) {
                $moduleActivationInfo = $moduleActivationInfo['ZikulaContentModule'];
                $isActivated = isset($moduleActivationInfo['active_' . $searchTypeCode]);
            }
            if (!$isActivated) {
                continue;
            }
    
            $objectType = $typeInfo['value'];
            $whereArray = [];
            $languageField = null;
            switch ($objectType) {
                case 'page':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.title';
                    $whereArray[] = 'tbl.metaDescription';
                    $whereArray[] = 'tbl.layout';
                    $whereArray[] = 'tbl.pageLanguage';
                    $whereArray[] = 'tbl.optionalString1';
                    $whereArray[] = 'tbl.optionalString2';
                    $whereArray[] = 'tbl.optionalText';
                    break;
                case 'contentItem':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.owningBundle';
                    $whereArray[] = 'tbl.owningType';
                    $whereArray[] = 'tbl.contentData';
                    $whereArray[] = 'tbl.scope';
                    $whereArray[] = 'tbl.stylePosition';
                    $whereArray[] = 'tbl.styleWidth';
                    $whereArray[] = 'tbl.styleClass';
                    break;
                case 'searchable':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.searchText';
                    $whereArray[] = 'tbl.searchLanguage';
                    break;
            }
    
            $repository = $this->entityFactory->getRepository($objectType);
    
            // build the search query without any joins
            $qb = $repository->genericBaseQuery('', '', false);
    
            // build where expression for given search type
            $whereExpr = $this->formatWhere($qb, $words, $whereArray, $searchType);
            $qb->andWhere($whereExpr);
    
            $query = $qb->getQuery();
    
            // set a sensitive limit
            $query->setFirstResult(0)
                  ->setMaxResults(250);
    
            // fetch the results
            $entities = $query->getResult();
    
            if (count($entities) == 0) {
                continue;
            }
    
            $descriptionFieldName = $this->entityDisplayHelper->getDescriptionFieldName($objectType);
            $hasDisplayAction = in_array($objectType, $entitiesWithDisplayAction);
    
            foreach ($entities as $entity) {
                // perform permission check
                if (!$this->permissionApi->hasPermission('ZikulaContentModule:' . ucfirst($objectType) . ':', $entity->getKey() . '::', ACCESS_OVERVIEW)) {
                    continue;
                }
    
                if (in_array($objectType, ['page'])) {
                    if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
                        if (!$this->categoryHelper->hasPermission($entity)) {
                            continue;
                        }
                    }
                }
    
                $description = !empty($descriptionFieldName) ? $entity[$descriptionFieldName] : '';
                $created = isset($entity['createdDate']) ? $entity['createdDate'] : null;
    
                $formattedTitle = $this->entityDisplayHelper->getFormattedTitle($entity);
                $displayUrl = '';
                if ($hasDisplayAction) {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = (null !== $languageField && !empty($entity[$languageField])) ? $entity[$languageField] : $this->request->getLocale();
                    $displayUrl = new RouteUrl('zikulacontentmodule_' . strtolower($objectType) . '_display', $urlArgs);
                }
    
                $result = new SearchResultEntity();
                $result->setTitle($formattedTitle)
                    ->setText($description)
                    ->setModule('ZikulaContentModule')
                    ->setCreated($created)
                    ->setSesid($this->session->getId())
                    ->setUrl($displayUrl);
                $results[] = $result;
            }
        }
    
        return $results;
    }
    
    /**
     * Returns list of supported search types.
     *
     * @return array List of search types
     */
    protected function getSearchTypes()
    {
        $searchTypes = [
            'zikulaContentModulePages' => [
                'value' => 'page',
                'label' => $this->__('Pages')
            ],
            'zikulaContentModuleContentItems' => [
                'value' => 'contentItem',
                'label' => $this->__('Content items')
            ],
            'zikulaContentModuleSearchables' => [
                'value' => 'searchable',
                'label' => $this->__('Searchables')
            ]
        ];
    
        $allowedTypes = $this->controllerHelper->getObjectTypes('helper', ['helper' => 'search', 'action' => 'getSearchTypes']);
        $allowedSearchTypes = [];
        foreach ($searchTypes as $searchType => $typeInfo) {
            if (!in_array($typeInfo['value'], $allowedTypes)) {
                continue;
            }
            $allowedSearchTypes[$searchType] = $typeInfo;
        }
    
        return $allowedSearchTypes;
    }
    
    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        return [];
    }
    
    /**
     * Construct a QueryBuilder Where orX|andX Expr instance.
     *
     * @param QueryBuilder $qb
     * @param string[] $words  List of words to query for
     * @param string[] $fields List of fields to include into query
     * @param string $searchtype AND|OR|EXACT
     *
     * @return null|Composite
     */
    protected function formatWhere(QueryBuilder $qb, array $words = [], array $fields = [], $searchtype = 'AND')
    {
        if (empty($words) || empty($fields)) {
            return null;
        }
    
        $method = ($searchtype == 'OR') ? 'orX' : 'andX';
        /** @var $where Composite */
        $where = $qb->expr()->$method();
        $i = 1;
        foreach ($words as $word) {
            $subWhere = $qb->expr()->orX();
            foreach ($fields as $field) {
                $expr = $qb->expr()->like($field, "?$i");
                $subWhere->add($expr);
                $qb->setParameter($i, '%' . $word . '%');
                $i++;
            }
            $where->add($subWhere);
        }
    
        return $where;
    }
}