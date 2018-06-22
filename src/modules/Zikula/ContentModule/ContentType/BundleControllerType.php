<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

/**
 * Bundle controller content type.
 */
class BundleControllerType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'cog';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Bundle controller');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display controller output from any installed module, theme or bundle.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'controller' => '',
            'arguments' => ''
        ];
    }

/** TODO
    function display()
    {
        static $recursionLevel = 0;
        if ($recursionLevel > 4) {
            return $this->__("Maximum number of pages-in-pages reached! You probably included this page in itself.");
        }

        // Convert "x=5,y=2" to array('x' => 5, 'y' => 2)
        $args = explode(',', $this->query);
        $arguments = array();
        foreach ($args as $arg) {
            if (!empty($arg)) {
                $argument = explode('=', $arg);
                $arguments[$argument[0]] = $argument[1];
            }
        }

        ++$recursionLevel;
        return ModUtil::func($this->module, $this->type, $this->func, $arguments);
        --$recursionLevel;
    }
    function displayEditing()
    {
        $output = "module=$this->module, type=$this->type, func=$this->func, query=$this->query";
        return $output;
    }
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}
