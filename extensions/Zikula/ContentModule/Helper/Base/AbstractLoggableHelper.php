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

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Loggable\LoggableListener;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ContentModule\Listener\EntityLifecycleListener;

/**
 * Helper base class for loggable behaviour.
 */
abstract class AbstractLoggableHelper
{
    use TranslatorTrait;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var EntityLifecycleListener
     */
    protected $entityLifecycleListener;
    
    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        EntityLifecycleListener $entityLifecycleListener,
        TranslatableHelper $translatableHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->entityLifecycleListener = $entityLifecycleListener;
        $this->translatableHelper = $translatableHelper;
    }
    
    /**
     * Determines template parameters for diff view.
     */
    public function determineDiffViewParameters(array $logEntries, array $versions): array
    {
        $minVersion = $maxVersion = 0;
        if ($versions[0] < $versions[1]) {
            $minVersion = $versions[0];
            $maxVersion = $versions[1];
        } else {
            $minVersion = $versions[1];
            $maxVersion = $versions[0];
        }
        $logEntries = array_reverse($logEntries);
    
        $diffValues = [];
        foreach ($logEntries as $logEntry) {
            if (null === $logEntry->getData()) {
                continue;
            }
            foreach ($logEntry->getData() as $field => $value) {
                if (!isset($diffValues[$field])) {
                    $diffValues[$field] = [
                        'old' => '',
                        'new' => '',
                        'changed' => false
                    ];
                }
                if ($logEntry->getVersion() <= $minVersion) {
                    $diffValues[$field]['old'] = $value;
                    $diffValues[$field]['new'] = $value;
                } elseif ($logEntry->getVersion() <= $maxVersion) {
                    $diffValues[$field]['new'] = $value;
                    $diffValues[$field]['changed'] = $diffValues[$field]['new'] !== $diffValues[$field]['old'];
                }
            }
        }
    
        return [$minVersion, $maxVersion, $diffValues];
    }
    
    /**
     * Return name of the version field for the given object type.
     */
    public function getVersionFieldName(string $objectType = ''): ?string
    {
        $versionFieldMap = [
            'page' => 'currentVersion',
        ];
    
        return $versionFieldMap[$objectType] ?? null;
    }
    
    /**
     * Checks whether a history may be shown for the given entity instance.
     */
    public function hasHistoryItems(EntityAccess $entity): bool
    {
        $objectType = $entity->get_objectType();
        $versionFieldName = $this->getVersionFieldName($objectType);
    
        if (null !== $versionFieldName) {
            $versionGetter = 'get' . ucfirst($versionFieldName);
    
            return 1 < $entity->$versionGetter();
        }
    
        // alternative (with worse performance)
        $entityManager = $this->entityFactory->getEntityManager();
        $logEntriesRepository = $entityManager->getRepository(
            'ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity'
        );
        $logEntries = $logEntriesRepository->getLogEntries($entity);
    
        return 1 < count($logEntries);
    }
    
    /**
     * Checks whether deleted entities exist for the given object type.
     */
    public function hasDeletedEntities(string $objectType = ''): bool
    {
        $entityManager = $this->entityFactory->getEntityManager();
        $logEntriesRepository = $entityManager->getRepository(
            'ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity'
        );
    
        return 0 < count($logEntriesRepository->selectDeleted(1));
    }
    
    /**
     * Returns deleted entities for the given object type.
     */
    public function getDeletedEntities(string $objectType = ''): array
    {
        $entityManager = $this->entityFactory->getEntityManager();
        $logEntriesRepository = $entityManager->getRepository(
            'ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity'
        );
    
        return $logEntriesRepository->selectDeleted();
    }
    
    /**
     * Sets the given entity to back to a specific version.
     */
    public function revert(EntityAccess $entity, int $requestedVersion = 1, bool $detach = false): EntityAccess
    {
        $entityManager = $this->entityFactory->getEntityManager();
        $objectType = $entity->get_objectType();
    
        $logEntriesRepository = $entityManager->getRepository(
            'ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity'
        );
        $logEntries = $logEntriesRepository->getLogEntries($entity);
        if (2 > count($logEntries)) {
            return $entity;
        }
    
        // revert to requested version
        $logEntriesRepository->revert($entity, $requestedVersion);
        if (true === $detach) {
            // detach the entity to avoid persisting it
            $entityManager->detach($entity);
        }
    
        return $this->revertPostProcess($entity);
    }
    
    /**
     * Resets a deleted entity back to the last version before it's deletion.
     */
    public function restoreDeletedEntity(string $objectType = '', int $id = 0): ?EntityAccess
    {
        if (!$id) {
            return null;
        }
    
        $methodName = 'create' . ucfirst($objectType);
        $entity = $this->entityFactory->$methodName();
        $idField = $this->entityFactory->getIdField($objectType);
        $setter = 'set' . ucfirst($idField);
        $entity->$setter($id);
    
        $entityManager = $this->entityFactory->getEntityManager();
        $logEntriesRepository = $entityManager->getRepository(
            'ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity'
        );
        $logEntries = $logEntriesRepository->getLogEntries($entity);
        $lastVersionBeforeDeletion = null;
        foreach ($logEntries as $logEntry) {
            if (LoggableListener::ACTION_REMOVE !== $logEntry->getAction()) {
                $lastVersionBeforeDeletion = $logEntry->getVersion();
                break;
            }
        }
        if (null === $lastVersionBeforeDeletion) {
            return null;
        }
    
        $objectType = $entity->get_objectType();
        $versionFieldName = $this->getVersionFieldName($objectType);
    
        $logEntriesRepository->revert($entity, $lastVersionBeforeDeletion);
        if (null !== $versionFieldName) {
            $versionSetter = 'set' . ucfirst($versionFieldName);
            $entity->$versionSetter($lastVersionBeforeDeletion + 2);
        }
    
        $entity->set_actionDescriptionForLogEntry(
            '_HISTORY_' . strtoupper($objectType) . '_RESTORED'
            . '|%version%=' . $lastVersionBeforeDeletion
        );
    
        return $this->revertPostProcess($entity);
    }
    
    /**
     * Performs actions after reverting an entity to a previous revision.
     */
    protected function revertPostProcess(EntityAccess $entity): EntityAccess
    {
        $objectType = $entity->get_objectType();
    
        if (in_array($objectType, ['page'], true)) {
            // check if parent is still valid
            $repository = $this->entityFactory->getRepository($objectType);
            $parentId = $entity->getParent()->getId();
            $parent = $parentId ? $repository->find($parentId) : null;
            if (in_array(Proxy::class, class_implements($parent), true)) {
                // look for a root node to use as parent
                $parentNode = $repository->findOneBy(['lvl' => 0]);
                $entity->setParent($parentNode);
            }
        }
    
        if (in_array($objectType, ['page'], true)) {
            $entity = $this->translatableHelper->setEntityFieldsFromLogData($entity);
        }
    
        $eventArgs = new LifecycleEventArgs($entity, $this->entityFactory->getEntityManager());
        $this->entityLifecycleListener->postLoad($eventArgs);
    
        return $entity;
    }
    
    /**
     * Persists a formerly entity again.
     *
     * @throws Exception If something goes wrong
     */
    public function undelete(EntityAccess $entity): void
    {
        $entityManager = $this->entityFactory->getEntityManager();
    
        $metadata = $entityManager->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    
        $versionField = $metadata->versionField;
        $metadata->setVersioned(false);
        $metadata->setVersionField(null);
    
        $entityManager->persist($entity);
        $entityManager->flush();
    
        $metadata->setVersioned(true);
        $metadata->setVersionField($versionField);
    }
    
    /**
     * Returns the translated clear text action description for a given log entry.
     */
    public function translateActionDescription(AbstractLogEntry $logEntry): string
    {
        $textAndParam = explode('|', $logEntry->getActionDescription());
        $text = $textAndParam[0];
        $parametersStr = 1 < count($textAndParam) ? $textAndParam[1] : '';
    
        $parameters = [];
        $parametersStr = explode(',', $parametersStr);
        foreach ($parametersStr as $parameterStr) {
            $varAndValue = explode('=', $parameterStr);
            if (2 === count($varAndValue)) {
                $parameters[$varAndValue[0]] = $varAndValue[1];
            }
        }
    
        return $this->translateActionDescriptionInternal($text, $parameters);
    }
    
    /**
     * Returns the translated clear text action description for a given log entry.
     */
    protected function translateActionDescriptionInternal(string $text = '', array $parameters = []): string
    {
        $actionTranslated = '';
        switch ($text) {
            case '_HISTORY_PAGE_CREATED':
                $actionTranslated = $this->trans('Page created', [], 'page');
                break;
            case '_HISTORY_PAGE_UPDATED':
                $actionTranslated = $this->trans('Page updated', [], 'page');
                break;
            case '_HISTORY_PAGE_CLONED':
                if (isset($parameters['%page%']) && is_numeric($parameters['%page%'])) {
                    $originalEntity = $this->entityFactory->getRepository('page')->selectById($parameters['%page%']);
                    if (null !== $originalEntity) {
                        $parameters['%page%'] = $this->entityDisplayHelper->getFormattedTitle($originalEntity);
                    }
                }
                $actionTranslated = $this->trans('Page cloned from page "%page%"', $parameters, [], 'page');
                break;
            case '_HISTORY_PAGE_RESTORED':
                $actionTranslated = $this->trans('Page restored from version "%version%"', $parameters, [], 'page');
                break;
            case '_HISTORY_PAGE_DELETED':
                $actionTranslated = $this->trans('Page deleted', [], 'page');
                break;
            case '_HISTORY_PAGE_TRANSLATION_CREATED':
                $actionTranslated = $this->trans('Page translation created', [], 'page');
                break;
            case '_HISTORY_PAGE_TRANSLATION_UPDATED':
                $actionTranslated = $this->trans('Page translation updated', [], 'page');
                break;
            case '_HISTORY_PAGE_TRANSLATION_DELETED':
                $actionTranslated = $this->trans('Page translation deleted', [], 'page');
                break;
            default:
                $actionTranslated = $text;
        }
    
        return $actionTranslated;
    }
}
