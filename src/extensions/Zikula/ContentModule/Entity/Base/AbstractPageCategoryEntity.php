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

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Zikula\CategoriesModule\Entity\AbstractCategoryAssignment;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * Entity extension domain class storing page categories.
 *
 * This is the base category class for page entities.
 */
abstract class AbstractPageCategoryEntity extends AbstractCategoryAssignment
{
    /**
     * @ORM\ManyToOne(targetEntity="\Zikula\ContentModule\Entity\PageEntity", inversedBy="categories")
     * @ORM\JoinColumn(name="entityId", referencedColumnName="id")
     *
     * @var PageEntity
     */
    protected $entity;
    
    public function getEntity(): PageEntity
    {
        return $this->entity;
    }
    
    public function setEntity($entity): void
    {
        if ($this->entity !== $entity) {
            $this->entity = $entity ?? '';
        }
    }
}
