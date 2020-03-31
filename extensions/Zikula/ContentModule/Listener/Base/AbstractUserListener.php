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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Event\GenericEvent;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\UsersModule\Event\ActiveUserPostCreatedEvent;
use Zikula\UsersModule\UserEvents;
use Zikula\ContentModule\Entity\Factory\EntityFactory;

/**
 * Event handler base class for user-related events.
 */
abstract class AbstractUserListener implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi,
        LoggerInterface $logger
    ) {
        $this->translator = $translator;
        $this->entityFactory = $entityFactory;
        $this->currentUserApi = $currentUserApi;
        $this->logger = $logger;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            ActiveUserPostCreatedEvent::class => ['create', 5],
            UserEvents::UPDATE_ACCOUNT => ['update', 5],
            UserEvents::DELETE_ACCOUNT => ['delete', 5]
        ];
    }
    
    /**
     * Listener for the `Zikula\UsersModule\Event\ActiveUserPostCreatedEvent` event.
     *
     * Occurs after a user account is created. All handlers are notified.
     * It does not apply to creation of a pending registration.
     * The full user record created is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record that was created.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     *
     * You can also access the user and date in the event.
     *
     * The user:
     *     `echo 'UID: ' . $event->getUser()->getUid();`
     */
    public function create(ActiveUserPostCreatedEvent $event): void
    {
    }
    
    /**
     * Listener for the `user.account.update` event.
     *
     * Occurs after a user is updated. All handlers are notified.
     * The full updated user record is available as the subject.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     * The subject of the event is set to the user record, with the updated values.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function update(GenericEvent $event): void
    {
    }
    
    /**
     * Listener for the `user.account.delete` event.
     *
     * Occurs after the deletion of a user account. Subject is $userId.
     * This is a storage-level event, not a UI event. It should not be used for UI-level actions such as redirects.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function delete(GenericEvent $event): void
    {
        $userId = (int) $event->getSubject();
    
        
        $repo = $this->entityFactory->getRepository('page');
        // set creator to admin (UsersConstant::USER_ID_ADMIN) for all pages created by this user
        $repo->updateCreator(
            $userId,
            UsersConstant::USER_ID_ADMIN,
            $this->translator,
            $this->logger,
            $this->currentUserApi
        );
        
        // set last editor to admin (UsersConstant::USER_ID_ADMIN) for all pages updated by this user
        $repo->updateLastEditor(
            $userId,
            UsersConstant::USER_ID_ADMIN,
            $this->translator,
            $this->logger,
            $this->currentUserApi
        );
        
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $this->currentUserApi->get('uname'),
            'entities' => 'pages'
        ];
        $this->logger->notice(
            '{app}: User {user} has been deleted, so we deleted/updated corresponding {entities}, too.',
            $logArgs
        );
        
        $repo = $this->entityFactory->getRepository('contentItem');
        // set creator to admin (UsersConstant::USER_ID_ADMIN) for all content items created by this user
        $repo->updateCreator(
            $userId,
            UsersConstant::USER_ID_ADMIN,
            $this->translator,
            $this->logger,
            $this->currentUserApi
        );
        
        // set last editor to admin (UsersConstant::USER_ID_ADMIN) for all content items updated by this user
        $repo->updateLastEditor(
            $userId,
            UsersConstant::USER_ID_ADMIN,
            $this->translator,
            $this->logger,
            $this->currentUserApi
        );
        
        $logArgs = [
            'app' => 'ZikulaContentModule',
            'user' => $this->currentUserApi->get('uname'),
            'entities' => 'content items'
        ];
        $this->logger->notice(
            '{app}: User {user} has been deleted, so we deleted/updated corresponding {entities}, too.',
            $logArgs
        );
    }
}
