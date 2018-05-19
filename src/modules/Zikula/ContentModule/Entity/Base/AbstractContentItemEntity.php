<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ContentModule\Traits\StandardFieldsTrait;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the base entity class for content item entities.
 * The following annotation marks it as a mapped superclass so subclasses
 * inherit orm properties.
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractContentItemEntity extends EntityAccess implements Translatable
{
    /**
     * Hook standard fields behaviour embedding createdBy, updatedBy, createdDate, updatedDate fields.
     */
    use StandardFieldsTrait;

    /**
     * @var string The tablename this object maps to
     */
    protected $_objectType = 'contentItem';
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var integer $id
     */
    protected $id = 0;
    
    /**
     * the current workflow state
     *
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="workflowState", multiple=false)
     * @var string $workflowState
     */
    protected $workflowState = 'initial';
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100000000000)
     * @var integer $areaIndex
     */
    protected $areaIndex = 0;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\NotNull()
     * @Assert\LessThan(value=100000000000)
     * @var integer $areaPosition
     */
    protected $areaPosition = 0;
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="100")
     * @var string $owningBundle
     */
    protected $owningBundle = '';
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="100")
     * @var string $owningType
     */
    protected $owningType = '';
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="text", length=9999999)
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="9999999")
     * @var text $contentData
     */
    protected $contentData = '';
    
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $active
     */
    protected $active = true;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\DateTime()
     * @var DateTime $activeFrom
     */
    protected $activeFrom;
    
    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\DateTime()
     * @Assert\Expression("!value or value > this.getActiveFrom()")
     * @var DateTime $activeTo
     */
    protected $activeTo;
    
    /**
     * @ORM\Column(length=1)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="scope", multiple=false)
     * @var string $scope
     */
    protected $scope = '1';
    
    /**
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="stylePosition", multiple=false)
     * @var string $stylePosition
     */
    protected $stylePosition = 'none';
    
    /**
     * @ORM\Column(length=20)
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="styleWidth", multiple=false)
     * @var string $styleWidth
     */
    protected $styleWidth = 'wauto';
    
    /**
     * @ORM\Column(length=100)
     * @Assert\NotNull()
     * @ContentAssert\ListEntry(entityName="contentItem", propertyName="styleClass", multiple=false)
     * @var string $styleClass
     */
    protected $styleClass = '';
    
    
    /**
     * Used locale to override Translation listener's locale.
     * this is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Locale()
     * @Gedmo\Locale
     * @var string $locale
     */
    protected $locale;
    
    /**
     * Bidirectional - Many contentItems [content items] are linked by one page [page] (OWNING SIDE).
     *
     * @ORM\ManyToOne(targetEntity="Zikula\ContentModule\Entity\PageEntity", inversedBy="contentItems", cascade={"persist"})
     * @ORM\JoinTable(name="zikula_content_page")
     * @Assert\Type(type="Zikula\ContentModule\Entity\PageEntity")
     * @var \Zikula\ContentModule\Entity\PageEntity $page
     */
    protected $page;
    
    /**
     * Bidirectional - One contentItem [content item] has many searchables [searchables] (INVERSE SIDE).
     *
     * @ORM\OneToMany(targetEntity="Zikula\ContentModule\Entity\SearchableEntity", mappedBy="contentItem")
     * @ORM\JoinTable(name="zikula_content_contentitemsearchables")
     * @var \Zikula\ContentModule\Entity\SearchableEntity[] $searchables
     */
    protected $searchables = null;
    
    
    /**
     * ContentItemEntity constructor.
     *
     * Will not be called by Doctrine and can therefore be used
     * for own implementation purposes. It is also possible to add
     * arbitrary arguments as with every other class method.
     */
    public function __construct()
    {
        $this->searchables = new ArrayCollection();
    }
    
    /**
     * Returns the _object type.
     *
     * @return string
     */
    public function get_objectType()
    {
        return $this->_objectType;
    }
    
    /**
     * Sets the _object type.
     *
     * @param string $_objectType
     *
     * @return void
     */
    public function set_objectType($_objectType)
    {
        if ($this->_objectType != $_objectType) {
            $this->_objectType = $_objectType;
        }
    }
    
    
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the id.
     *
     * @param integer $id
     *
     * @return void
     */
    public function setId($id)
    {
        if (intval($this->id) !== intval($id)) {
            $this->id = intval($id);
        }
    }
    
    /**
     * Returns the workflow state.
     *
     * @return string
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }
    
    /**
     * Sets the workflow state.
     *
     * @param string $workflowState
     *
     * @return void
     */
    public function setWorkflowState($workflowState)
    {
        if ($this->workflowState !== $workflowState) {
            $this->workflowState = isset($workflowState) ? $workflowState : '';
        }
    }
    
    /**
     * Returns the area index.
     *
     * @return integer
     */
    public function getAreaIndex()
    {
        return $this->areaIndex;
    }
    
    /**
     * Sets the area index.
     *
     * @param integer $areaIndex
     *
     * @return void
     */
    public function setAreaIndex($areaIndex)
    {
        if (intval($this->areaIndex) !== intval($areaIndex)) {
            $this->areaIndex = intval($areaIndex);
        }
    }
    
    /**
     * Returns the area position.
     *
     * @return integer
     */
    public function getAreaPosition()
    {
        return $this->areaPosition;
    }
    
    /**
     * Sets the area position.
     *
     * @param integer $areaPosition
     *
     * @return void
     */
    public function setAreaPosition($areaPosition)
    {
        if (intval($this->areaPosition) !== intval($areaPosition)) {
            $this->areaPosition = intval($areaPosition);
        }
    }
    
    /**
     * Returns the owning bundle.
     *
     * @return string
     */
    public function getOwningBundle()
    {
        return $this->owningBundle;
    }
    
    /**
     * Sets the owning bundle.
     *
     * @param string $owningBundle
     *
     * @return void
     */
    public function setOwningBundle($owningBundle)
    {
        if ($this->owningBundle !== $owningBundle) {
            $this->owningBundle = isset($owningBundle) ? $owningBundle : '';
        }
    }
    
    /**
     * Returns the owning type.
     *
     * @return string
     */
    public function getOwningType()
    {
        return $this->owningType;
    }
    
    /**
     * Sets the owning type.
     *
     * @param string $owningType
     *
     * @return void
     */
    public function setOwningType($owningType)
    {
        if ($this->owningType !== $owningType) {
            $this->owningType = isset($owningType) ? $owningType : '';
        }
    }
    
    /**
     * Returns the content data.
     *
     * @return text
     */
    public function getContentData()
    {
        return $this->contentData;
    }
    
    /**
     * Sets the content data.
     *
     * @param text $contentData
     *
     * @return void
     */
    public function setContentData($contentData)
    {
        if ($this->contentData !== $contentData) {
            $this->contentData = isset($contentData) ? $contentData : '';
        }
    }
    
    /**
     * Returns the active.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * Sets the active.
     *
     * @param boolean $active
     *
     * @return void
     */
    public function setActive($active)
    {
        if (boolval($this->active) !== boolval($active)) {
            $this->active = boolval($active);
        }
    }
    
    /**
     * Returns the active from.
     *
     * @return DateTime
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }
    
    /**
     * Sets the active from.
     *
     * @param DateTime $activeFrom
     *
     * @return void
     */
    public function setActiveFrom($activeFrom)
    {
        if ($this->activeFrom !== $activeFrom) {
            if (!(null == $activeFrom && empty($activeFrom)) && !(is_object($activeFrom) && $activeFrom instanceOf \DateTimeInterface)) {
                $activeFrom = new \DateTime($activeFrom);
            }
            
            if (null === $activeFrom || empty($activeFrom)) {
                $activeFrom = new \DateTime();
            }
            
            if ($this->activeFrom != $activeFrom) {
                $this->activeFrom = $activeFrom;
            }
        }
    }
    
    /**
     * Returns the active to.
     *
     * @return DateTime
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }
    
    /**
     * Sets the active to.
     *
     * @param DateTime $activeTo
     *
     * @return void
     */
    public function setActiveTo($activeTo)
    {
        if ($this->activeTo !== $activeTo) {
            if (!(null == $activeTo && empty($activeTo)) && !(is_object($activeTo) && $activeTo instanceOf \DateTimeInterface)) {
                $activeTo = new \DateTime($activeTo);
            }
            
            if (null === $activeTo || empty($activeTo)) {
                $activeTo = new \DateTime();
            }
            
            if ($this->activeTo != $activeTo) {
                $this->activeTo = $activeTo;
            }
        }
    }
    
    /**
     * Returns the scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * Sets the scope.
     *
     * @param string $scope
     *
     * @return void
     */
    public function setScope($scope)
    {
        if ($this->scope !== $scope) {
            $this->scope = isset($scope) ? $scope : '';
        }
    }
    
    /**
     * Returns the style position.
     *
     * @return string
     */
    public function getStylePosition()
    {
        return $this->stylePosition;
    }
    
    /**
     * Sets the style position.
     *
     * @param string $stylePosition
     *
     * @return void
     */
    public function setStylePosition($stylePosition)
    {
        if ($this->stylePosition !== $stylePosition) {
            $this->stylePosition = isset($stylePosition) ? $stylePosition : '';
        }
    }
    
    /**
     * Returns the style width.
     *
     * @return string
     */
    public function getStyleWidth()
    {
        return $this->styleWidth;
    }
    
    /**
     * Sets the style width.
     *
     * @param string $styleWidth
     *
     * @return void
     */
    public function setStyleWidth($styleWidth)
    {
        if ($this->styleWidth !== $styleWidth) {
            $this->styleWidth = isset($styleWidth) ? $styleWidth : '';
        }
    }
    
    /**
     * Returns the style class.
     *
     * @return string
     */
    public function getStyleClass()
    {
        return $this->styleClass;
    }
    
    /**
     * Sets the style class.
     *
     * @param string $styleClass
     *
     * @return void
     */
    public function setStyleClass($styleClass)
    {
        if ($this->styleClass !== $styleClass) {
            $this->styleClass = isset($styleClass) ? $styleClass : '';
        }
    }
    
    /**
     * Returns the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Sets the locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        if ($this->locale != $locale) {
            $this->locale = $locale;
        }
    }
    
    
    /**
     * Returns the page.
     *
     * @return \Zikula\ContentModule\Entity\PageEntity
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Sets the page.
     *
     * @param \Zikula\ContentModule\Entity\PageEntity $page
     *
     * @return void
     */
    public function setPage($page = null)
    {
        $this->page = $page;
    }
    
    /**
     * Returns the searchables.
     *
     * @return \Zikula\ContentModule\Entity\SearchableEntity[]
     */
    public function getSearchables()
    {
        return $this->searchables;
    }
    
    /**
     * Sets the searchables.
     *
     * @param \Zikula\ContentModule\Entity\SearchableEntity[] $searchables
     *
     * @return void
     */
    public function setSearchables($searchables)
    {
        foreach ($this->searchables as $searchableSingle) {
            $this->removeSearchables($searchableSingle);
        }
        foreach ($searchables as $searchableSingle) {
            $this->addSearchables($searchableSingle);
        }
    }
    
    /**
     * Adds an instance of \Zikula\ContentModule\Entity\SearchableEntity to the list of searchables.
     *
     * @param \Zikula\ContentModule\Entity\SearchableEntity $searchable The instance to be added to the collection
     *
     * @return void
     */
    public function addSearchables(\Zikula\ContentModule\Entity\SearchableEntity $searchable)
    {
        $this->searchables->add($searchable);
        $searchable->setContentItem($this);
    }
    
    /**
     * Removes an instance of \Zikula\ContentModule\Entity\SearchableEntity from the list of searchables.
     *
     * @param \Zikula\ContentModule\Entity\SearchableEntity $searchable The instance to be removed from the collection
     *
     * @return void
     */
    public function removeSearchables(\Zikula\ContentModule\Entity\SearchableEntity $searchable)
    {
        $this->searchables->removeElement($searchable);
        $searchable->setContentItem(null);
    }
    
    
    
    /**
     * Creates url arguments array for easy creation of display urls.
     *
     * @return array List of resulting arguments
     */
    public function createUrlArgs()
    {
        return [
            'id' => $this->getId()
        ];
    }
    
    /**
     * Returns the primary key.
     *
     * @return integer The identifier
     */
    public function getKey()
    {
        return $this->getId();
    }
    
    /**
     * Determines whether this entity supports hook subscribers or not.
     *
     * @return boolean
     */
    public function supportsHookSubscribers()
    {
        return true;
    }
    
    /**
     * Return lower case name of multiple items needed for hook areas.
     *
     * @return string
     */
    public function getHookAreaPrefix()
    {
        return 'zikulacontentmodule.ui_hooks.contentitems';
    }
    
    /**
     * Returns an array of all related objects that need to be persisted after clone.
     * 
     * @param array $objects Objects that are added to this array
     * 
     * @return array List of entity objects
     */
    public function getRelatedObjectsToPersist(&$objects = [])
    {
        return [];
    }
    
    /**
     * ToString interceptor implementation.
     * This method is useful for debugging purposes.
     *
     * @return string The output string for this entity
     */
    public function __toString()
    {
        return 'Content item ' . $this->getKey() . ': ' . $this->getOwningBundle();
    }
    
    /**
     * Clone interceptor implementation.
     * This method is for example called by the reuse functionality.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // otherwise proceed
    
        // unset identifier
        $this->setId(0);
    
        // reset workflow
        $this->setWorkflowState('initial');
    
        $this->setCreatedBy(null);
        $this->setCreatedDate(null);
        $this->setUpdatedBy(null);
        $this->setUpdatedDate(null);
    
    }
}