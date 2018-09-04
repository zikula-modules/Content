<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Gedmo\Loggable\LoggableListener;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\Doctrine\EntityAccess;
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
    
    /**
     * LoggableHelper constructor.
     *
     * @param TranslatorInterface     $translator              Translator service instance
     * @param EntityFactory           $entityFactory           EntityFactory service instance
     * @param EntityDisplayHelper     $entityDisplayHelper     EntityDisplayHelper service instance
     * @param EntityLifecycleListener $entityLifecycleListener Entity lifecycle subscriber
     * @param TranslatableHelper      $translatableHelper      TranslatableHelper service instance
     */
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
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Determines template parameters for diff view.
     *
     * @param array $logEntries List of log entries for currently treated entity instance
     * @param array $versions   List of desired version numbers
     *
     * @return array
     */
    public function determineDiffViewParameters($logEntries, $versions)
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
                    $diffValues[$field]['changed'] = $diffValues[$field]['new'] != $diffValues[$field]['old'];
                }
            }
        }
    
        return [$minVersion, $maxVersion, $diffValues];
    }
    
    /**
     * Return name of the version field for the given object type.
     *
     * @param string $objectType Currently treated entity type
     *
     * @return string|null
     */
    public function getVersionFieldName($objectType = '')
    {
        $versionFieldMap = [
            'page' => 'currentVersion',
        ];
    
        return isset($versionFieldMap[$objectType]) ? $versionFieldMap[$objectType] : null;
    }
    
    /**
     * Checks whether a history may be shown for the given entity instance.
     *
     * @param EntityAccess $entity Currently treated entity instance
     *
     * @return boolean
     */
    public function hasHistoryItems($entity)
    {
        $objectType = $entity->get_objectType();
        $versionFieldName = $this->getVersionFieldName($objectType);
    
        if (null !== $versionFieldName) {
            $versionGetter = 'get' . ucfirst($versionFieldName);
    
            return $entity->$versionGetter() > 1;
        }
    
        // alternative (with worse performance)
        $entityManager = $this->entityFactory->getObjectManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity');
        $logEntries = $logEntriesRepository->getLogEntries($entity);
    
        return count($logEntries) > 1;
    }
    
    /**
     * Checks whether deleted entities exist for the given object type.
     *
     * @param string $objectType Currently treated entity type
     *
     * @return boolean
     */
    public function hasDeletedEntities($objectType = '')
    {
        $entityManager = $this->entityFactory->getObjectManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity');
    
        return count($logEntriesRepository->selectDeleted(1)) > 0;
    }
    
    /**
     * Returns deleted entities for the given object type.
     *
     * @param string $objectType Currently treated entity type
     *
     * @return array
     */
    public function getDeletedEntities($objectType = '')
    {
        $entityManager = $this->entityFactory->getObjectManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity');
    
        return $logEntriesRepository->selectDeleted();
    }
    
    /**
     * Sets the given entity to back to a specific version.
     *
     * @param EntityAccess $entity           Currently treated entity instance
     * @param integer      $requestedVersion Target version
     * @param boolean      $detach           Whether to detach the entity or not
     *
     * @return EntityAccess The reverted entity instance
     */
    public function revert($entity, $requestedVersion = 1, $detach = false)
    {
        $entityManager = $this->entityFactory->getObjectManager();
        $objectType = $entity->get_objectType();
    
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity');
        $logEntries = $logEntriesRepository->getLogEntries($entity);
        if (count($logEntries) < 2) {
            return $entity;
        }
    
        // revert to requested version
        $logEntriesRepository->revert($entity, $requestedVersion);
        if (true === $detach) {
            // detach the entity to avoid persisting it
            $entityManager->detach($entity);
        }
    
        $entity = $this->revertPostProcess($entity);
    
        return $entity;
    }
    
    /**
     * Resets a deleted entity back to the last version before it's deletion.
     *
     * @param string  $objectType Currently treated entity type
     * @param integer $id         The entity's identifier
     *
     * @return EntityAccess|null The restored entity instance
     */
    public function restoreDeletedEntity($objectType = '', $id = 0)
    {
        if (!$id) {
            return null;
        }
    
        $methodName = 'create' . ucfirst($objectType);
        $entity = $this->entityFactory->$methodName();
        $idField = $this->entityFactory->getIdField($objectType);
        $setter = 'set' . ucfirst($idField);
        $entity->$setter($id);
    
        $entityManager = $this->entityFactory->getObjectManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . ucfirst($objectType) . 'LogEntryEntity');
        $logEntries = $logEntriesRepository->getLogEntries($entity);
        $lastVersionBeforeDeletion = null;
        foreach ($logEntries as $logEntry) {
            if (LoggableListener::ACTION_REMOVE != $logEntry->getAction()) {
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
    
        $entity->set_actionDescriptionForLogEntry('_HISTORY_' . strtoupper($objectType) . '_RESTORED|%version=' . $lastVersionBeforeDeletion);
    
        $entity = $this->revertPostProcess($entity);
    
        return $entity;
    }
    
    /**
     * Performs actions after reverting an entity to a previous revision.
     *
     * @param EntityAccess $entity Currently treated entity instance
     *
     * @return EntityAccess The processed entity instance
     */
    protected function revertPostProcess($entity)
    {
        $objectType = $entity->get_objectType();
    
        if (in_array($objectType, ['page'])) {
            // check if parent is still valid
            $repository = $this->entityFactory->getRepository($objectType);
            $parentId = $entity->getParent()->getId();
            $parent = $parentId ? $repository->find($parentId) : null;
            if (in_array('Doctrine\Common\Proxy\Proxy', class_implements($parent), true)) {
                // look for a root node to use as parent
                $parentNode = $repository->findOneBy(['lvl' => 0]);
                $entity->setParent($parentNode);
            }
        }
    
        if (in_array($objectType, ['page'])) {
            $entity = $this->translatableHelper->setEntityFieldsFromLogData($entity);
        }
    
        $eventArgs = new LifecycleEventArgs($entity, $this->entityFactory->getObjectManager());
        $this->entityLifecycleListener->postLoad($eventArgs);
    
        return $entity;
    }
    
    /**
     * Persists a formerly entity again.
     *
     * @param EntityAccess $entity Currently treated entity instance
     *
     * @return EntityAccess|null The restored entity instance
     *
     * @throws Exception If something goes wrong
     */
    public function undelete($entity)
    {
        $entityManager = $this->entityFactory->getObjectManager();
    
        $metadata = $entityManager->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    
        $versionField = $metadata->versionField;
        $metadata->setVersioned(false);
        $metadata->setVersionField(null);
    
        $entityManager->persist($entity);
        $entityManager->flush($entity);
    
        $metadata->setVersioned(true);
        $metadata->setVersionField($versionField);
    }
    
    /**
     * Returns the translated clear text action description for a given log entry.
     *
     * @param AbstractLogEntry $logEntry
     *
     * @return string
     */
    public function translateActionDescription(AbstractLogEntry $logEntry)
    {
        $textAndParam = explode('|', $logEntry->getActionDescription());
        $text = $textAndParam[0];
        $parametersStr = count($textAndParam) > 1 ? $textAndParam[1] : '';
    
        $parameters = [];
        $parametersStr = explode(',', $parametersStr);
        foreach ($parametersStr as $parameterStr) {
            $varAndValue = explode('=', $parameterStr);
            if (2 == count($varAndValue)) {
                $parameters[$varAndValue[0]] = $varAndValue[1];
            }
        }
    
        return $this->translateActionDescriptionInternal($text, $parameters);
    }
    
    /**
     * Returns the translated clear text action description for a given log entry.
     *
     * @param string $text       The constant which is replaced by a corresponding Gettext call
     * @param array  $parameters Optional additional parameters for the Gettext call
     *
     * @return string The resulting description
     */
    protected function translateActionDescriptionInternal($text = '', array $parameters = [])
    {
        $this->translator->setDomain('zikulacontentmodule');
        $actionTranslated = '';
        switch ($text) {
            case '_HISTORY_PAGE_CREATED':
                $actionTranslated = $this->__('Page created');
                break;
            case '_HISTORY_PAGE_UPDATED':
                $actionTranslated = $this->__('Page updated');
                break;
            case '_HISTORY_PAGE_CLONED':
                if (isset($parameters['%page']) && is_numeric($parameters['%page'])) {
                    $originalEntity = $this->entityFactory->getRepository('page')->selectById($parameters['%page']);
                    if (null !== $originalEntity) {
                        $parameters['%page'] = $this->entityDisplayHelper->getFormattedTitle($originalEntity);
                    }
                }
                $actionTranslated = $this->__f('Page cloned from page "%page"', $parameters);
                break;
            case '_HISTORY_PAGE_RESTORED':
                $actionTranslated = $this->__f('Page restored from version "%version"', $parameters);
                break;
            case '_HISTORY_PAGE_DELETED':
                $actionTranslated = $this->__('Page deleted');
                break;
            case '_HISTORY_PAGE_TRANSLATION_CREATED':
                $actionTranslated = $this->__('Page translation created');
                break;
            case '_HISTORY_PAGE_TRANSLATION_UPDATED':
                $actionTranslated = $this->__('Page translation updated');
                break;
            case '_HISTORY_PAGE_TRANSLATION_DELETED':
                $actionTranslated = $this->__('Page translation deleted');
                break;
            default:
                $actionTranslated = $text;
        }
    
        return $actionTranslated;
    }
}