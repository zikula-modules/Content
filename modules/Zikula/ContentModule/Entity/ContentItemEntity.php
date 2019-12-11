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

namespace Zikula\ContentModule\Entity;

use DateTime;
use Zikula\ContentModule\Entity\Base\AbstractContentItemEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for content item entities.
 * @Gedmo\TranslationEntity(class="Zikula\ContentModule\Entity\ContentItemTranslationEntity")
 * @ORM\Entity(repositoryClass="Zikula\ContentModule\Entity\Repository\ContentItemRepository")
 * @ORM\Table(name="zikula_content_contentitem",
 *     indexes={
 *         @ORM\Index(name="activeindex", columns={"active"}),
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class ContentItemEntity extends BaseEntity
{
    /**
     * Checks whether this content item is currently active or not.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->getActive()) {
            return false;
        }

        $activeFrom = $this->getActiveFrom();
        $activeTo = $this->getActiveTo();
        $now = new DateTime();
        if (null !== $activeFrom && $activeFrom > $now) {
            return false;
        }
        if (null !== $activeTo && $activeTo < $now) {
            return false;
        }

        return true;
    }
}
