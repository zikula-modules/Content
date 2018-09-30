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

namespace Zikula\ContentModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\Event\GenericEvent;
use Zikula\UsersModule\AccessEvents;

/**
 * Event handler base class for user logout events.
 */
abstract class AbstractUserLogoutListener implements EventSubscriberInterface
{
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            AccessEvents::LOGOUT_SUCCESS => ['succeeded', 5]
        ];
    }
    
    /**
     * Listener for the `module.users.ui.logout.succeeded` event.
     *
     * Occurs right after a successful logout. All handlers are notified.
     * The event's subject contains the user's UserEntity.
     * Args contain array of `['authentication_method' => $authenticationMethod,
     *                         'uid'                   => $uid];`
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     * @param GenericEvent $event The event instance
     */
    public function succeeded(GenericEvent $event)
    {
    }
}
