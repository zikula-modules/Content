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
 * Unfiltered raw content type.
 */
class UnfilteredType extends AbstractContentType
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
        return 'user-secret';
    }

    /**
     * @inheritDoc
     */
    function getTitle()
    {
        return $this->__('Unfiltered raw text');
    }

    /**
     * @inheritDoc
     */
    function getDescription()
    {
        return $this->__('A plugin for unfiltered raw output (iframes, JavaScript, banners, etc).');
    }

    /**
     * @inheritDoc
     */
    public function getAdminInfo()
    {
        // TODO
        return $this->__('You need to explicitly enable a checkbox in the configuration form to activate this plugin.');
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        // Only active when the admin has enabled this plugin
        // TODO
        return true;
        return false;//(bool) ModUtil::getVar('ZikulaContentModule', 'enableRawPlugin', false);
    }

    /**
     * @inheritDoc
     */
    function isTranslatable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'text' => $this->__('Add unfiltered text here ...'),
            'useiframe' => false,
            'iframeTitle' => '',
            'iframeName' => '',
            'iframeSrc' => '',
            'iframeStyle' => 'border:0',
            'iframeWidth' => 800,
            'iframeHeight' => 600,
            'iframeBorder' => 0,
            'iframeScrolling' => 'no',
            'iframeAllowTransparancy' => true
        ];
    }

/** TODO
    function displayEditing()
    {
        if ($this->useiframe) {
            $output = '<div style="background-color:Lavender; padding:10px;">' . $this->__f('An <strong>iframe</strong> is included with<br />src = %1$s<br />width = %2$s and height = %3$s', array($this->iframesrc, $this->iframewidth, $this->iframeheight)) . '</div>';
        } else {
            $output = '<div style="background-color:Lavender; padding:10px;">' . $this->__f('The following <strong>unfiltered text</strong> will be included literally<br />%s', DataUtil::formatForDisplay($this->text)) . '</div>';
        }
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
