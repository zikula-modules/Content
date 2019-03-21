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

namespace Zikula\ContentModule\Entity\Factory\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use Zikula\ContentModule\Entity\Factory\EntityInitialiser;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Factory class used to create entities and receive entity repositories.
 */
abstract class AbstractEntityFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityInitialiser The entity initialiser for dynamic application of default values
     */
    protected $entityInitialiser;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    /**
     * EntityFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityInitialiser $entityInitialiser
     * @param CollectionFilterHelper $collectionFilterHelper
     * @param FeatureActivationHelper $featureActivationHelper
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityInitialiser $entityInitialiser,
        CollectionFilterHelper $collectionFilterHelper,
        FeatureActivationHelper $featureActivationHelper)
    {
        $this->entityManager = $entityManager;
        $this->entityInitialiser = $entityInitialiser;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Returns a repository for a given object type.
     *
     * @param string $objectType Name of desired entity type
     *
     * @return EntityRepository The repository responsible for the given object type
     */
    public function getRepository($objectType)
    {
        $entityClass = 'Zikula\\ContentModule\\Entity\\' . ucfirst($objectType) . 'Entity';

        $repository = $this->getEntityManager()->getRepository($entityClass);
        $repository->setCollectionFilterHelper($this->collectionFilterHelper);

        if (in_array($objectType, ['page', 'contentItem'])) {
            $repository->setTranslationsEnabled($this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, $objectType));
        }

        return $repository;
    }

    /**
     * Creates a new page instance.
     *
     * @return \Zikula\ContentModule\Entity\PageEntity The newly created entity instance
     */
    public function createPage()
    {
        $entityClass = 'Zikula\\ContentModule\\Entity\\PageEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initPage($entity);

        return $entity;
    }

    /**
     * Creates a new contentItem instance.
     *
     * @return \Zikula\ContentModule\Entity\ContentItemEntity The newly created entity instance
     */
    public function createContentItem()
    {
        $entityClass = 'Zikula\\ContentModule\\Entity\\ContentItemEntity';

        $entity = new $entityClass();

        $this->entityInitialiser->initContentItem($entity);

        return $entity;
    }

    /**
     * Returns the identifier field's name for a given object type.
     *
     * @param string $objectType The object type to be treated
     *
     * @return string Primary identifier field name
     */
    public function getIdField($objectType = '')
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException('Invalid object type received.');
        }
        $entityClass = 'ZikulaContentModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->getEntityManager()->getClassMetadata($entityClass);
    
        return $meta->getSingleIdentifierFieldName();
    }

    /**
     * Returns the entity manager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * Sets the entity manager.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return void
     */
    public function setEntityManager($entityManager)
    {
        if ($this->entityManager != $entityManager) {
            $this->entityManager = $entityManager;
        }
    }
    

    /**
     * Returns the entity initialiser.
     *
     * @return EntityInitialiser
     */
    public function getEntityInitialiser()
    {
        return $this->entityInitialiser;
    }
    
    /**
     * Sets the entity initialiser.
     *
     * @param EntityInitialiser $entityInitialiser
     *
     * @return void
     */
    public function setEntityInitialiser($entityInitialiser)
    {
        if ($this->entityInitialiser != $entityInitialiser) {
            $this->entityInitialiser = $entityInitialiser;
        }
    }
    
}
