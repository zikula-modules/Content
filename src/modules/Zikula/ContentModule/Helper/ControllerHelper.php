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

namespace Zikula\ContentModule\Helper;

use Zikula\ContentModule\Helper\Base\AbstractControllerHelper;

/**
 * Helper implementation class for controller layer methods.
 */
class ControllerHelper extends AbstractControllerHelper
{
    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    /**
     * Sets the content display helper.
     *
     * @param ContentDisplayHelper $displayHelper
     */
    public function setContentDisplayHelper(ContentDisplayHelper $displayHelper)
    {
        $this->displayHelper = $displayHelper;
    }

    /**
     * @inheritDoc
     */
    public function processDisplayActionParameters($objectType, array $templateParameters = [], $hasHookSubscriber = false)
    {
        if ('page' == $objectType) {
            $entity = $templateParameters[$objectType];
            $hasHookSubscriber = !$entity->getSkipUiHookSubscriber();
        }
        $parameters = parent::processDisplayActionParameters($objectType, $templateParameters, $hasHookSubscriber);
        if ('page' == $objectType) {
            $parameters['contentElements'] = [];
            foreach ($entity->getContentItems() as $contentItem) {
                $parameters['contentElements'][$contentItem->getId()] = $this->displayHelper->getDetailsForDisplayView($contentItem);
            }
        }

        return $parameters;
    }
}
