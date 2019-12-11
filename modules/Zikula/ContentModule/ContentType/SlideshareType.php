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

namespace Zikula\ContentModule\ContentType;

use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\SlideshareType as FormType;
use Zikula\ContentModule\Helper\CacheHelper;

/**
 * Slideshare content type.
 */
class SlideshareType extends AbstractContentType
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
        return 'slideshare';
    }

    public function getTitle(): string
    {
        return $this->__('Slideshare');
    }

    public function getDescription(): string
    {
        return $this->__('Display slides from slideshare.com.');
    }

    public function getDefaultData(): array
    {
        return [
            'url' => '',
            'text' => '',
            'width' => 599,
            'height' => 487
        ];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    public function displayView(): string
    {
        $this->data['slideUrl'] = '';
        $this->data['details'] = '';

        if (isset($this->data['url']) && '' !== $this->data['url']) {
            $content = $this->cacheHelper->fetch(
                'https://www.slideshare.net/api/oembed/2'
                . '?url=' . $this->data['url']
                . '&format=json'
            );
            if (false !== $content) {
                $this->data['details'] = @json_decode($content, true);
                // see https://www.beliefmedia.com.au/slideshare-wordpress
                /* Since building new embed code (due scaling), we'll snatch embed URL */
                $html = $this->data['details']['html'];
                $regex = '$\b(https?)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i';
                preg_match_all($regex, $html, $result);
                $this->data['slideUrl'] = $result['0']['0'] ?? '';
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
