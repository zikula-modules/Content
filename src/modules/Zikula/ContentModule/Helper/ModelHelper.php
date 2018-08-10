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

use Zikula\ContentModule\Helper\Base\AbstractModelHelper;

/**
 * Helper implementation class for model layer methods.
 */
class ModelHelper extends AbstractModelHelper
{
    /**
     * @inheritDocs
     */
    public function resolveSortParameter($objectType = '', $sorting = 'default')
    {
        if ('page' == $objectType && 'views' == $sorting) {
            return 'views DESC';
        }

        return parent::resolveSortParameter($objectType, $sorting);
    }
}
