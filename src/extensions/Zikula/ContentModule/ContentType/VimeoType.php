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

use Zikula\ContentModule\ContentType\Form\Type\VimeoType as FormType;
use Zikula\ContentModule\Helper\CacheHelper;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Vimeo content type.
 */
class VimeoType extends AbstractContentType
{
    /**
     * @var CacheHelper
     */
    protected $cacheHelper;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_EXTERNAL;
    }

    public function getIcon(): string
    {
        return 'fab fa-vimeo';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Vimeo video', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Display a Vimeo video clip.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        return [
            'url' => '',
            'text' => '',
            'videoId' => '',
            'displayMode' => 'inline',
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['url', 'text'];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text'] ?? ''));
    }

    public function displayView(): string
    {
        $this->data['videoId'] = '';
        $this->data['details'] = '';
        $r = '/vimeo.com\/([-a-zA-Z0-9_]+)/';
        if (isset($this->data['url']) && '' !== $this->data['url'] && preg_match($r, $this->data['url'], $matches)) {
            $this->data['videoId'] = $matches[1];
            $content = $this->cacheHelper->fetch(
                'https://vimeo.com/api/v2/video/'
                . $this->data['videoId'] . '.php'
            );
            if (false !== $content) {
                $this->data['details'] = @unserialize($content);
            }
        }

        return parent::displayView();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * @required
     */
    public function setCacheHelper(CacheHelper $cacheHelper): void
    {
        $this->cacheHelper = $cacheHelper;
    }
}
