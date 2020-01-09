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

use Gedmo\Loggable\LoggableListener;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\SettingsModule\Api\ApiInterface\LocaleApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;

/**
 * Helper base class for translatable methods.
 */
abstract class AbstractTranslatableHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var LocaleApiInterface
     */
    protected $localeApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var LoggableListener
     */
    protected $loggableListener;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        LocaleApiInterface $localeApi,
        EntityFactory $entityFactory
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->localeApi = $localeApi;
        $this->entityFactory = $entityFactory;
        $this->loggableListener = null;
    }
    
    /**
     * Return list of translatable fields per entity.
     * These are required to be determined to recognise
     * that they have to be selected from according translation tables.
     */
    public function getTranslatableFields(string $objectType): array
    {
        $fields = [];
        switch ($objectType) {
            case 'page':
                $fields = ['title', 'metaDescription', 'optionalString1', 'optionalString2', 'optionalText', 'slug'];
                break;
            case 'contentItem':
                $fields = ['contentData', 'searchText', 'additionalSearchText'];
                break;
        }
    
        return $fields;
    }
    
    /**
     * Return the current language code.
     */
    public function getCurrentLanguage(): string
    {
        $request = $this->requestStack->getCurrentRequest();
    
        return null !== $request ? $request->getLocale() : 'en';
    }
    
    /**
     * Return list of supported languages on the current system.
     */
    public function getSupportedLanguages(string $objectType): array
    {
        if ($this->variableApi->getSystemVar('multilingual')) {
            return $this->localeApi->getSupportedLocales();
        }
    
        // if multi language is disabled use only the current language
        return [$this->getCurrentLanguage()];
    }
    
    /**
     * Returns a list of mandatory fields for each supported language.
     */
    public function getMandatoryFields(string $objectType): array
    {
        $mandatoryFields = [];
        foreach ($this->getSupportedLanguages($objectType) as $language) {
            $mandatoryFields[$language] = [];
        }
    
        return $mandatoryFields;
    }
    
    /**
     * Collects translated fields for editing.
     *
     * @return array Collected translations for each language code
     */
    public function prepareEntityForEditing(EntityAccess $entity): array
    {
        $translations = [];
        $objectType = $entity->get_objectType();
    
        if (!$this->variableApi->getSystemVar('multilingual')) {
            return $translations;
        }
    
        // check if there are any translated fields registered for the given object type
        $fields = $this->getTranslatableFields($objectType);
        if (!count($fields)) {
            return $translations;
        }
    
        // get translations
        $entityManager = $this->entityFactory->getEntityManager();
        $repository = $entityManager->getRepository(
            'Zikula\ContentModule\Entity\\' . ucfirst($objectType) . 'TranslationEntity'
        );
        $entityTranslations = $repository->findTranslations($entity);
    
        $supportedLanguages = $this->getSupportedLanguages($objectType);
        $currentLanguage = $this->getCurrentLanguage();
        foreach ($supportedLanguages as $language) {
            if ($language === $currentLanguage) {
                foreach ($fields as $fieldName) {
                    if (null === $entity[$fieldName]) {
                        $entity[$fieldName] = '';
                    }
                }
                // skip current language as this is not treated as translation on controller level
                continue;
            }
            $translationData = [];
            foreach ($fields as $fieldName) {
                $translationData[$fieldName] = $entityTranslations[$language][$fieldName] ?? '';
            }
            if (isset($translationData['slug']) && in_array($objectType, ['page'])) {
                $slugParts = explode('/', $translationData['slug']);
                $translationData['slug'] = end($slugParts);
            }
            // add data to collected translations
            $translations[$language] = $translationData;
        }
    
        return $translations;
    }
    
    /**
     * Post-editing method persisting translated fields.
     */
    public function processEntityAfterEditing(EntityAccess $entity, FormInterface $form): void
    {
        $this->toggleLoggable(false);
    
        $objectType = $entity->get_objectType();
        $entityManager = $this->entityFactory->getEntityManager();
        $supportedLanguages = $this->getSupportedLanguages($objectType);
        foreach ($supportedLanguages as $language) {
            $translationInput = $this->readTranslationInput($form, $language);
            if (!count($translationInput)) {
                continue;
            }
    
            foreach ($translationInput as $fieldName => $fieldData) {
                $setter = 'set' . ucfirst($fieldName);
                $entity->$setter($fieldData);
            }
    
            $entity->setLocale($language);
            $entityManager->flush();
        }
    
        $this->toggleLoggable(true);
    }
    
    /**
     * Collects translated fields from given form for a specific language.
     */
    public function readTranslationInput(FormInterface $form, string $language = 'en'): array
    {
        $data = [];
        $translationKey = 'translations' . $language;
        if (!isset($form[$translationKey])) {
            return $data;
        }
        $translatedFields = $form[$translationKey];
        foreach ($translatedFields as $fieldName => $formField) {
            $fieldData = $formField->getData();
            if (!$fieldData && isset($form[$fieldName])) {
                $fieldData = $form[$fieldName]->getData();
            }
            $data[$fieldName] = $fieldData;
        }
    
        return $data;
    }
    
    /**
     * Enables or disables the loggable listener to avoid log entries
     * for translation changes.
     */
    public function toggleLoggable(bool $enable = true): void
    {
        $eventManager = $this->entityFactory->getEntityManager()->getEventManager();
        if (null === $this->loggableListener) {
            foreach ($eventManager->getListeners() as $event => $listeners) {
                foreach ($listeners as $hash => $listener) {
                    if ($listener instanceof LoggableListener) {
                        $this->loggableListener = $listener;
                        break 2;
                    }
                }
            }
        }
        if (null === $this->loggableListener) {
            return;
        }
    
        if (true === $enable) {
            $eventManager->addEventSubscriber($this->loggableListener);
        } else {
            $eventManager->removeEventSubscriber($this->loggableListener);
        }
    }
    
    /**
     * Sets values for translatable fields of given entity from it's stored
     * translation data.
     */
    public function setEntityFieldsFromLogData(EntityAccess $entity): EntityAccess
    {
        // check if this revision has translation data for current locale
        $translationData = $entity->getTranslationData();
        $language = $this->getCurrentLanguage();
        if (!isset($translationData[$language])) {
            return $entity;
        }
    
        $objectType = $entity->get_objectType();
        $translatableFields = $this->getTranslatableFields($objectType);
        foreach ($translatableFields as $fieldName) {
            if (!isset($translationData[$language][$fieldName])) {
                continue;
            }
            $setter = 'set' . ucfirst($fieldName);
            $entity->$setter($translationData[$language][$fieldName]);
        }
    
        return $entity;
    }
    
    /**
     * Removes all translations and persists them again for all
     * translatable fields of given entity from it's stored
     * translation data.
     *
     * The logic of this method is similar to processEntityAfterEditing above.
     */
    public function refreshTranslationsFromLogData(EntityAccess $entity): void
    {
        $this->toggleLoggable(false);
    
        $objectType = $entity->get_objectType();
    
        // remove all existing translations
        $entityManager = $this->entityFactory->getEntityManager();
        $translationClass = 'Zikula\ContentModule\Entity\\' . ucfirst($objectType) . 'TranslationEntity';
        $repository = $entityManager->getRepository($translationClass);
        $translationMeta = $entityManager->getClassMetadata($translationClass);
        $qb = $entityManager->createQueryBuilder();
        $qb->delete($translationMeta->rootEntityName, 'trans')
           ->where('trans.objectClass = :objectClass')
           ->andWhere('trans.foreignKey = :objectId')
           ->setParameter('objectClass', get_class($entity))
           ->setParameter('objectId', $entity->getKey())
        ;
        $query = $qb->getQuery();
        $query->execute();
    
        $translatableFields = $this->getTranslatableFields($objectType);
        $translationData = $entity->getTranslationData();
        $supportedLanguages = $this->getSupportedLanguages($objectType);
        foreach ($supportedLanguages as $language) {
            // check if this revision has translation data for current locale
            if (!isset($translationData[$language])) {
                continue;
            }
    
            foreach ($translatableFields as $fieldName) {
                if (!isset($translationData[$language][$fieldName])) {
                    continue;
                }
                $setter = 'set' . ucfirst($fieldName);
                $entity->$setter($translationData[$language][$fieldName]);
            }
    
            $entity->setLocale($language);
            $entityManager->flush();
        }
    
        $this->toggleLoggable(true);
    }
}
