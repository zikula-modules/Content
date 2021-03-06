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

namespace Zikula\ContentModule\Event\Base;

use Zikula\ContentModule\Entity\ContentItemEntity;

/**
 * Event base class for filtering content item processing.
 */
abstract class AbstractContentItemPreUpdateEvent
{
    /**
     * @var ContentItemEntity Reference to treated entity instance
     */
    protected $contentItem;

    /**
     * @var array Entity change set for preUpdate events
     */
    protected $entityChangeSet = [];

    public function __construct(ContentItemEntity $contentItem, array $entityChangeSet = [])
    {
        $this->contentItem = $contentItem;
        $this->entityChangeSet = $entityChangeSet;
    }

    public function getContentItem(): ContentItemEntity
    {
        return $this->contentItem;
    }

    /**
     * @return array Entity change set
     */
    public function getEntityChangeSet(): array
    {
        return $this->entityChangeSet;
    }

    /**
     * @param array $changeSet Entity change set
     */
    public function setEntityChangeSet(array $changeSet = []): void
    {
        $this->entityChangeSet = $changeSet;
    }
}
