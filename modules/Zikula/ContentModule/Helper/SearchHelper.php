<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper;

use Symfony\Component\Form\FormBuilderInterface;
use Zikula\ContentModule\Helper\Base\AbstractSearchHelper;
use Zikula\Core\RouteUrl;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\SearchModule\Entity\SearchResultEntity;

/**
 * Search helper implementation class.
 */
class SearchHelper extends AbstractSearchHelper
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    public function amendForm(FormBuilderInterface $builder): void
    {
        /*$builder->add('active', CheckboxType::class, [
            'data' => true,
            'label' => $this->__('Content pages')
        ]);*/
    }

    public function getResults(array $words, string $searchType = 'AND', array $modVars = null): array
    {
        if (!$this->permissionHelper->hasPermission(ACCESS_READ)) {
            return [];
        }

        // initialise array for results
        $results = [];

        $objectType = 'page';
        $whereArray = [
            'tbl.title',
            'tbl.metaDescription'
        ];
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString1')) {
            $whereArray[] = 'tbl.optionalString1';
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString2')) {
            $whereArray[] = 'tbl.optionalString2';
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalText')) {
            $whereArray[] = 'tbl.optionalText';
        }
        $whereArray[] = 'tblContentItems.searchText';
        $whereArray[] = 'tblContentItems.additionalSearchText';

        $repository = $this->entityFactory->getRepository($objectType);

        // build the search query without any joins
        $qb = $repository->getListQueryBuilder();

        // build where expression for given search type
        $whereExpr = $this->formatWhere($qb, $words, $whereArray, $searchType);
        $qb->andWhere($whereExpr);

        $query = $repository->getQueryFromBuilder($qb);

        // set a sensitive limit
        $query->setFirstResult(0)
              ->setMaxResults(250);

        // fetch the results
        $entities = $query->getResult();
        if (0 === count($entities)) {
            return $results;
        }

        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        foreach ($entities as $entity) {
            if (!$this->permissionHelper->mayRead($entity)) {
                continue;
            }

            if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
                if (!$this->categoryHelper->hasPermission($entity)) {
                    continue;
                }
            }

            $formattedTitle = $this->entityDisplayHelper->getFormattedTitle($entity);
            $urlArgs = $entity->createUrlArgs();
            $urlArgs['_locale'] = $request->getLocale();
            $displayUrl = new RouteUrl('zikulacontentmodule_' . strtolower($objectType) . '_display', $urlArgs);

            $result = new SearchResultEntity();
            $result->setTitle($formattedTitle)
                ->setText($entity['metaDescription'])
                ->setModule($this->getBundleName())
                ->setCreated($entity['createdDate'])
                ->setSesid($session->getId())
                ->setUrl($displayUrl);
            $results[] = $result;
        }

        return $results;
    }

    /**
     * @required
     */
    public function setVariableApi(VariableApiInterface $variableApi): void
    {
        $this->variableApi = $variableApi;
    }
}
