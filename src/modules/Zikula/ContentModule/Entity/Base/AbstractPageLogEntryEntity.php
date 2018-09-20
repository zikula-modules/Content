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

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Entity extension domain class storing page log entries.
 *
 * This is the base log entry class for page entities.
 */
abstract class AbstractPageLogEntryEntity extends AbstractLogEntry
{
    /**
     * Extended description of the executed action which produced this log entry.
     *
     * @var string $actionDescription
     *
     * @ORM\Column(name="action_description", length=255)
     */
    protected $actionDescription = '';
    
    /**
     * Returns the action description.
     *
     * @return string
     */
    public function getActionDescription()
    {
        return $this->actionDescription;
    }
    
    /**
     * Sets the action description.
     *
     * @param string $actionDescription
     *
     * @return void
     */
    public function setActionDescription($actionDescription)
    {
        if ($this->actionDescription !== $actionDescription) {
            $this->actionDescription = isset($actionDescription) ? $actionDescription : '';
        }
    }
}
