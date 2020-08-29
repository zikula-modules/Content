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
abstract class AbstractContentItemPostLoadEvent
{
    /**
     * @var ContentItemEntity Reference to treated entity instance
     */
    protected $contentItem;

    public function __construct(ContentItemEntity $contentItem)
    {
        $this->contentItem = $contentItem;
    }

    public function getContentItem(): ContentItemEntity
    {
        return $this->contentItem;
    }
}
