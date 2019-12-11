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

namespace Zikula\ContentModule\Block;

use Zikula\ContentModule\Block\Base\AbstractItemListBlock;

/**
 * Generic item list block implementation class.
 */
class ItemListBlock extends AbstractItemListBlock
{
    protected function getDefaults(): array
    {
        $defaults = parent::getDefaults();
        $defaults['root'] = 0;
        $defaults['inMenu'] = true;

        return $defaults;
    }

    protected function resolveCategoryIds(array $properties = []): array
    {
        $properties = parent::resolveCategoryIds($properties);

        $customFilters = [];
        if (0 < $properties['root']) {
            $customFilters[] = 'tbl.parent = ' . $properties['root'];
        /*} else {
            $customFilters[] = 'tbl.parent IS NULL';*/
        }
        if (true === $properties['inMenu']) {
            $customFilters[] = 'tbl.inMenu = 1';
        }

        if (count($customFilters) > 0) {
            if (!empty($properties['filter'])) {
                $properties['filter'] = '(' . $properties['filter'] . ') AND ' . implode(' AND ', $customFilters);
            } else {
                $properties['filter'] = implode(' AND ', $customFilters);
            }
        }

        return $properties;
    }
}
