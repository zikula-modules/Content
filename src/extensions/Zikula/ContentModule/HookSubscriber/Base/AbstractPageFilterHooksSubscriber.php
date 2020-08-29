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

namespace Zikula\ContentModule\HookSubscriber\Base;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\HookBundle\Category\FilterHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;

/**
 * Base class for filter hooks subscriber.
 */
abstract class AbstractPageFilterHooksSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner(): string
    {
        return 'ZikulaContentModule';
    }
    
    public function getCategory(): string
    {
        return FilterHooksCategory::NAME;
    }
    
    public function getTitle(): string
    {
        return $this->translator->trans('Page filter hooks subscriber', [], 'hooks');
    }
    
    public function getAreaName(): string
    {
        return 'subscriber.zikulacontentmodule.filter_hooks.pages';
    }

    public function getEvents(): array
    {
        return [
            FilterHooksCategory::TYPE_FILTER => 'zikulacontentmodule.filter_hooks.pages.filter',
        ];
    }
}
