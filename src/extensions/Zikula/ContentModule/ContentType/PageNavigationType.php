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

namespace Zikula\ContentModule\ContentType;

use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Page navigation content type.
 */
class PageNavigationType extends AbstractContentType
{
    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'fas fa-map-signs';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Page navigation', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Allows to navigate within pages on the same level.', [], 'contentTypes');
    }

    public function displayView(): string
    {
        $this->data['page'] = $this->getEntity()->getPage();

        return parent::displayView();
    }
}
