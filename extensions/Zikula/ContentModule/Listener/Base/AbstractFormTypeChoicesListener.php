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

namespace Zikula\ContentModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Bundle\FormExtensionBundle\Event\FormTypeChoiceEvent;

/**
 * Event handler base class for injecting custom dynamic form types.
 */
abstract class AbstractFormTypeChoicesListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormTypeChoiceEvent::NAME => ['formTypeChoices']
        ];
    }
    
    /**
     * Listener for the `FormTypeChoiceEvent` event.
     *
     * Implement using like this:
     *
     * $choices = $event->getChoices();
     *
     * $groupName = $this->translator->trans('Other Fields');
     * if (!isset($choices[$groupName])) {
     *     $choices[$groupName] = [];
     * }
     *
     * $groupChoices = $choices[$groupName];
     * $groupChoices[$this->translator->trans('Special field')] = SpecialFieldType::class;
     * $choices[$groupName] = $groupChoices;
     *
     * $event->setChoices($choices);
     */
    public function formTypeChoices(FormTypeChoiceEvent $event): void
    {
    }
}
