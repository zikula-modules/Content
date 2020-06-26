<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Helper\Base;

use Zikula\ContentModule\Entity\Factory\EntityFactory;

/**
 * Helper base class for model layer methods.
 */
abstract class AbstractModelHelper
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * Determines whether creating an instance of a certain object type is possible.
     * This is when
     *     - it has no incoming bidirectional non-nullable relationships.
     *     - the edit type of all those relationships has PASSIVE_EDIT and auto completion is used on the target side
     *       (then a new source object can be created while creating the target object).
     *     - corresponding source objects exist already in the system.
     *
     * Note that even creation of a certain object is possible, it may still be forbidden for the current user
     * if he does not have the required permission level.
     */
    public function canBeCreated(string $objectType = ''): bool
    {
        $result = false;
    
        switch ($objectType) {
            case 'page':
                $result = true;
                break;
        }
    
        return $result;
    }
    
    /**
     * Determines whether there exists at least one instance of a certain object type in the database.
     */
    protected function hasExistingInstances(string $objectType = ''): bool
    {
        $repository = $this->entityFactory->getRepository($objectType);
        if (null === $repository) {
            return false;
        }
    
        return 0 < $repository->selectCount();
    }
    
    /**
     * Returns a desired sorting criteria for passing it to a repository method.
     */
    public function resolveSortParameter(string $objectType = '', string $sorting = 'default'): string
    {
        if ('random' === $sorting) {
            return 'RAND()';
        }
    
        $hasStandardFields = in_array($objectType, ['page', 'contentItem']);
    
        $sortParam = '';
        if ('newest' === $sorting) {
            if (true === $hasStandardFields) {
                $sortParam = 'createdDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ('updated' === $sorting) {
            if (true === $hasStandardFields) {
                $sortParam = 'updatedDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ('default' === $sorting) {
            $repository = $this->entityFactory->getRepository($objectType);
            $sortParam = $repository->getDefaultSortingField();
        }
    
        return $sortParam;
    }
}
