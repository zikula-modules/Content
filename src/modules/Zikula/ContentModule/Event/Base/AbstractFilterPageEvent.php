<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Event\Base;

use Symfony\Component\EventDispatcher\Event;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * Event base class for filtering page processing.
 */
class AbstractFilterPageEvent extends Event
{
    /**
     * @var PageEntity Reference to treated entity instance.
     */
    protected $page;

    /**
     * @var array Entity change set for preUpdate events.
     */
    protected $entityChangeSet = [];

    /**
     * FilterPageEvent constructor.
     *
     * @param PageEntity $page Processed entity
     * @param array $entityChangeSet Change set for preUpdate events
     */
    public function __construct(PageEntity $page, array $entityChangeSet = [])
    {
        $this->page = $page;
        $this->entityChangeSet = $entityChangeSet;
    }

    /**
     * Returns the entity.
     *
     * @return PageEntity
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Returns the change set.
     *
     * @return array Entity change set
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }
}
