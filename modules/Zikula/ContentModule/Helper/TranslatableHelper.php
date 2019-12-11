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

use RuntimeException;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\Base\AbstractTranslatableHelper;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Helper implementation class for translatable methods.
 */
class TranslatableHelper extends AbstractTranslatableHelper
{
    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    public function prepareEntityForEditing(EntityAccess $entity): array
    {
        $translations = [];
        if (!$this->variableApi->getSystemVar('multilingual')) {
            return $translations;
        }

        $translations = parent::prepareEntityForEditing($entity);
        $objectType = $entity->get_objectType();
        if ('contentItem' !== $objectType) {
            return $translations;
        }

        foreach ($translations as $language => $translationData) {
            if (isset($translationData['contentData']) && !is_array($translationData['contentData'])) {
                if (empty($translationData['contentData'])) {
                    $translations[$language]['contentData'] = [];
                } else {
                    $translations[$language]['contentData'] = unserialize($translationData['contentData']);
                }
            }
        }

        return $translations;
    }

    /**
     * Returns information about which page elements are translatable.
     */
    public function getTranslationInfo(PageEntity $page, ContentItemEntity $contentItem = null): array
    {
        $result = [
            'items' => [],
            'currentContentId' => null,
            'previousContentId' => null,
            'nextContentId' => null
        ];
        $pageContentItems = $page->getContentItems();
        if (!count($pageContentItems)) {
            return $result;
        }

        // reorder content items by page layout data
        $layoutData = $page->getLayout();
        $processedItemIds = [];
        if (is_array($layoutData) && count($layoutData) > 0) {
            $pageContentItemsOrdered = [];
            foreach ($layoutData as $sectionKey => $section) {
                if (!isset($section['widgets']) || !is_array($section['widgets']) || !count($section['widgets'])) {
                    continue;
                }
                foreach ($section['widgets'] as $widgetKey => $widget) {
                    foreach ($pageContentItems as $item) {
                        if ($widget['id'] !== $item->getId()) {
                            continue;
                        }
                        $pageContentItemsOrdered[] = $item;
                        $processedItemIds[] = $widget['id'];
                        break;
                    }
                }
            }
            if (count($processedItemIds) < count($pageContentItemsOrdered)) {
                foreach ($pageContentItems as $item) {
                    if (in_array($item->getId(), $processedItemIds, true)) {
                        continue;
                    }
                    $pageContentItemsOrdered[] = $item;
                }
            }
            $pageContentItems = $pageContentItemsOrdered;
        }

        $contentItems = [];
        foreach ($pageContentItems as $item) {
            try {
                $contentItems[] = $this->displayHelper->initContentType($item);
            } catch (RuntimeException $exception) {
                // ignore
            }
        }

        $currentIndex = -1;
        if (null !== $contentItem) {
            $i = 1;
            foreach ($contentItems as $contentType) {
                if ($contentItem->getId() !== $contentType->getEntity()->getId()) {
                    $i++;
                    continue;
                }
                $currentIndex = $i - 1;
                break;
            }
        }

        $result['items'] = $contentItems;

        $amountOfItems = count($contentItems);
        if (null !== $contentItem) {
            if ($currentIndex < $amountOfItems - 1) {
                $result['nextContentId'] = $contentItems[$currentIndex + 1]->getEntity()->getId();
            }
            if (0 < $currentIndex) {
                $result['previousContentId'] = $contentItems[$currentIndex - 1]->getEntity()->getId();
            }
        } else {
            if (0 < $amountOfItems) {
                $result['nextContentId'] = $contentItems[0]->getEntity()->getId();
            }
        }
        if (-1 < $currentIndex) {
            $result['currentContentId'] = $contentItems[$currentIndex]->getEntity()->getId();
        }

        return $result;
    }

    /**
     * @required
     */
    public function setContentDisplayHelper(ContentDisplayHelper $displayHelper): void
    {
        $this->displayHelper = $displayHelper;
    }

    /**
     * Removes all obsolete content item translations.
     */
    public function cleanupTranslationsForContentItems(): void
    {
        $this->toggleLoggable(false);
    
        $objectType = 'contentItem';
        $connection = $this->entityFactory->getEntityManager()->getConnection();
        $connection->executeQuery('
            DELETE FROM `zikula_content_' . strtolower($objectType) . '_translation`
            WHERE `foreign_key` NOT IN (
                SELECT `id` FROM `zikula_content_' . strtolower($objectType) . '`
            )
        ');
    
        $this->toggleLoggable(true);
    }
}
