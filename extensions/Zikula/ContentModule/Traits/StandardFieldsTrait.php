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

namespace Zikula\ContentModule\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Standard fields trait.
 */
trait StandardFieldsTrait
{
    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zikula\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(referencedColumnName="uid")
     * @var UserEntity
     */
    protected $createdBy;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Assert\Type("\DateTimeInterface")
     * @var DateTimeInterface $createdDate
     */
    protected $createdDate;
    
    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Zikula\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(referencedColumnName="uid")
     * @var UserEntity
     */
    protected $updatedBy;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Assert\Type("\DateTimeInterface")
     * @var DateTimeInterface $updatedDate
     */
    protected $updatedDate;
    
    public function getCreatedBy(): ?UserEntity
    {
        return $this->createdBy;
    }
    
    public function setCreatedBy(UserEntity $createdBy = null): void
    {
        if ($this->createdBy !== $createdBy) {
            $this->createdBy = $createdBy;
        }
    }
    
    public function getCreatedDate(): ?DateTimeInterface
    {
        return $this->createdDate;
    }
    
    public function setCreatedDate(DateTimeInterface $createdDate = null): void
    {
        if ($this->createdDate !== $createdDate) {
            $this->createdDate = $createdDate;
        }
    }
    
    public function getUpdatedBy(): ?UserEntity
    {
        return $this->updatedBy;
    }
    
    public function setUpdatedBy(UserEntity $updatedBy = null): void
    {
        if ($this->updatedBy !== $updatedBy) {
            $this->updatedBy = $updatedBy;
        }
    }
    
    public function getUpdatedDate(): ?DateTimeInterface
    {
        return $this->updatedDate;
    }
    
    public function setUpdatedDate(DateTimeInterface $updatedDate = null): void
    {
        if ($this->updatedDate !== $updatedDate) {
            $this->updatedDate = $updatedDate;
        }
    }
}
