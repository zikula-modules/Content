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

namespace Zikula\ContentModule\Helper\Base;

/**
 * Helper base class for dynamic feature enablement methods.
 */
abstract class AbstractFeatureActivationHelper
{
    /**
     * Categorisation feature
     */
    const CATEGORIES = 'categories';
    
    /**
     * Translation feature
     */
    const TRANSLATIONS = 'translations';
    
    /**
     * Tree relatives feature
     */
    const TREE_RELATIVES = 'treeRelatives';
    
    /**
     * This method checks whether a certain feature is enabled for a given entity type or not.
     *
     * @param string $feature     Name of requested feature
     * @param string $objectType  Currently treated entity type
     *
     * @return boolean True if the feature is enabled, false otherwise
     */
    public function isEnabled($feature, $objectType)
    {
        if (self::CATEGORIES == $feature) {
            $method = 'hasCategories';
            if (method_exists($this, $method)) {
                return $this->$method($objectType);
            }
    
            return in_array($objectType, ['page']);
        }
        if (self::TRANSLATIONS == $feature) {
            $method = 'hasTranslations';
            if (method_exists($this, $method)) {
                return $this->$method($objectType);
            }
    
            return in_array($objectType, ['page', 'contentItem']);
        }
        if (self::TREE_RELATIVES == $feature) {
            $method = 'hasTreeRelatives';
            if (method_exists($this, $method)) {
                return $this->$method($objectType);
            }
    
            return in_array($objectType, ['page']);
        }
    
        return false;
    }
}
