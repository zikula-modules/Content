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

namespace Zikula\ContentModule\Base;

use Symfony\Component\Validator\Constraints as Assert;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Validator\Constraints as ContentAssert;

/**
 * Application settings class for handling module variables.
 */
abstract class AbstractAppSettings
{
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @Assert\NotBlank()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="stateOfNewPages", multiple=false)
     * @var string $stateOfNewPages
     */
    protected $stateOfNewPages = '1';
    
    /**
     * Page views are only counted when the user has no edit access. Enable if you want to use the block showing most viewed pages.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $countPageViews
     */
    protected $countPageViews = false;
    
    /**
     * If you want to use Google maps you need an API key for it. You should enable both "Maps JavaScript API" and "Maps Static API".
     *
     * @Assert\NotNull()
     * @Assert\Length(min="0", max="255")
     * @var string $googleMapsApiKey
     */
    protected $googleMapsApiKey = '';
    
    /**
     * Whether to enable the unfiltered raw text plugin. Use this plugin with caution and if you can trust your editors, since no filtering is being done on the content. To be used for iframes, JavaScript blocks, etc.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableRawPlugin
     */
    protected $enableRawPlugin = false;
    
    /**
     * A list of CSS class names available for styling of content elements. The end user can select these classes for each element on a page - for instance "note" for an element styled as a note. Write one class name on each line. Please separate the CSS classes and displaynames with | - eg. "note | Memo".
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="0", max="5000")
     * @var text $stylingClasses
     */
    protected $stylingClasses = 'greybox|Grey box';
    
    /**
     * Whether to inherit permissions from parent to child pages or not.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $inheritPermissions
     */
    protected $inheritPermissions = false;
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalString1
     */
    protected $enableOptionalString1 = false;
    
    /**
     * If you need an additional string for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalString2
     */
    protected $enableOptionalString2 = false;
    
    /**
     * If you need an additional text for each page you can enable an optional field.
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $enableOptionalText
     */
    protected $enableOptionalText = false;
    
    /**
     * The amount of pages shown per page
     *
     * @Assert\Type(type="integer")
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value=0)
     * @Assert\LessThan(value=100000000000)
     * @var integer $pageEntriesPerPage
     */
    protected $pageEntriesPerPage = 10;
    
    /**
     * Whether to add a link to pages of the current user on his account page
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $linkOwnPagesOnAccountPage
     */
    protected $linkOwnPagesOnAccountPage = true;
    
    /**
     * Whether only own entries should be shown on view pages by default or not
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @var boolean $showOnlyOwnEntries
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * Which sections are supported in the Finder component (used by Scribite plug-ins).
     *
     * @Assert\NotNull()
     * @ContentAssert\ListEntry(entityName="appSettings", propertyName="enabledFinderTypes", multiple=true)
     * @var string $enabledFinderTypes
     */
    protected $enabledFinderTypes = 'page';
    
    
    /**
     * AppSettings constructor.
     *
     * @param VariableApiInterface $variableApi VariableApi service instance
     */
    public function __construct(
        VariableApiInterface $variableApi
    ) {
        $this->variableApi = $variableApi;
    
        $this->load();
    }
    
    /**
     * Returns the state of new pages.
     *
     * @return string
     */
    public function getStateOfNewPages()
    {
        return $this->stateOfNewPages;
    }
    
    /**
     * Sets the state of new pages.
     *
     * @param string $stateOfNewPages
     *
     * @return void
     */
    public function setStateOfNewPages($stateOfNewPages)
    {
        if ($this->stateOfNewPages !== $stateOfNewPages) {
            $this->stateOfNewPages = isset($stateOfNewPages) ? $stateOfNewPages : '';
        }
    }
    
    /**
     * Returns the count page views.
     *
     * @return boolean
     */
    public function getCountPageViews()
    {
        return $this->countPageViews;
    }
    
    /**
     * Sets the count page views.
     *
     * @param boolean $countPageViews
     *
     * @return void
     */
    public function setCountPageViews($countPageViews)
    {
        if (boolval($this->countPageViews) !== boolval($countPageViews)) {
            $this->countPageViews = boolval($countPageViews);
        }
    }
    
    /**
     * Returns the google maps api key.
     *
     * @return string
     */
    public function getGoogleMapsApiKey()
    {
        return $this->googleMapsApiKey;
    }
    
    /**
     * Sets the google maps api key.
     *
     * @param string $googleMapsApiKey
     *
     * @return void
     */
    public function setGoogleMapsApiKey($googleMapsApiKey)
    {
        if ($this->googleMapsApiKey !== $googleMapsApiKey) {
            $this->googleMapsApiKey = isset($googleMapsApiKey) ? $googleMapsApiKey : '';
        }
    }
    
    /**
     * Returns the enable raw plugin.
     *
     * @return boolean
     */
    public function getEnableRawPlugin()
    {
        return $this->enableRawPlugin;
    }
    
    /**
     * Sets the enable raw plugin.
     *
     * @param boolean $enableRawPlugin
     *
     * @return void
     */
    public function setEnableRawPlugin($enableRawPlugin)
    {
        if (boolval($this->enableRawPlugin) !== boolval($enableRawPlugin)) {
            $this->enableRawPlugin = boolval($enableRawPlugin);
        }
    }
    
    /**
     * Returns the styling classes.
     *
     * @return text
     */
    public function getStylingClasses()
    {
        return $this->stylingClasses;
    }
    
    /**
     * Sets the styling classes.
     *
     * @param text $stylingClasses
     *
     * @return void
     */
    public function setStylingClasses($stylingClasses)
    {
        if ($this->stylingClasses !== $stylingClasses) {
            $this->stylingClasses = isset($stylingClasses) ? $stylingClasses : '';
        }
    }
    
    /**
     * Returns the inherit permissions.
     *
     * @return boolean
     */
    public function getInheritPermissions()
    {
        return $this->inheritPermissions;
    }
    
    /**
     * Sets the inherit permissions.
     *
     * @param boolean $inheritPermissions
     *
     * @return void
     */
    public function setInheritPermissions($inheritPermissions)
    {
        if (boolval($this->inheritPermissions) !== boolval($inheritPermissions)) {
            $this->inheritPermissions = boolval($inheritPermissions);
        }
    }
    
    /**
     * Returns the enable optional string 1.
     *
     * @return boolean
     */
    public function getEnableOptionalString1()
    {
        return $this->enableOptionalString1;
    }
    
    /**
     * Sets the enable optional string 1.
     *
     * @param boolean $enableOptionalString1
     *
     * @return void
     */
    public function setEnableOptionalString1($enableOptionalString1)
    {
        if (boolval($this->enableOptionalString1) !== boolval($enableOptionalString1)) {
            $this->enableOptionalString1 = boolval($enableOptionalString1);
        }
    }
    
    /**
     * Returns the enable optional string 2.
     *
     * @return boolean
     */
    public function getEnableOptionalString2()
    {
        return $this->enableOptionalString2;
    }
    
    /**
     * Sets the enable optional string 2.
     *
     * @param boolean $enableOptionalString2
     *
     * @return void
     */
    public function setEnableOptionalString2($enableOptionalString2)
    {
        if (boolval($this->enableOptionalString2) !== boolval($enableOptionalString2)) {
            $this->enableOptionalString2 = boolval($enableOptionalString2);
        }
    }
    
    /**
     * Returns the enable optional text.
     *
     * @return boolean
     */
    public function getEnableOptionalText()
    {
        return $this->enableOptionalText;
    }
    
    /**
     * Sets the enable optional text.
     *
     * @param boolean $enableOptionalText
     *
     * @return void
     */
    public function setEnableOptionalText($enableOptionalText)
    {
        if (boolval($this->enableOptionalText) !== boolval($enableOptionalText)) {
            $this->enableOptionalText = boolval($enableOptionalText);
        }
    }
    
    /**
     * Returns the page entries per page.
     *
     * @return integer
     */
    public function getPageEntriesPerPage()
    {
        return $this->pageEntriesPerPage;
    }
    
    /**
     * Sets the page entries per page.
     *
     * @param integer $pageEntriesPerPage
     *
     * @return void
     */
    public function setPageEntriesPerPage($pageEntriesPerPage)
    {
        if (intval($this->pageEntriesPerPage) !== intval($pageEntriesPerPage)) {
            $this->pageEntriesPerPage = intval($pageEntriesPerPage);
        }
    }
    
    /**
     * Returns the link own pages on account page.
     *
     * @return boolean
     */
    public function getLinkOwnPagesOnAccountPage()
    {
        return $this->linkOwnPagesOnAccountPage;
    }
    
    /**
     * Sets the link own pages on account page.
     *
     * @param boolean $linkOwnPagesOnAccountPage
     *
     * @return void
     */
    public function setLinkOwnPagesOnAccountPage($linkOwnPagesOnAccountPage)
    {
        if (boolval($this->linkOwnPagesOnAccountPage) !== boolval($linkOwnPagesOnAccountPage)) {
            $this->linkOwnPagesOnAccountPage = boolval($linkOwnPagesOnAccountPage);
        }
    }
    
    /**
     * Returns the show only own entries.
     *
     * @return boolean
     */
    public function getShowOnlyOwnEntries()
    {
        return $this->showOnlyOwnEntries;
    }
    
    /**
     * Sets the show only own entries.
     *
     * @param boolean $showOnlyOwnEntries
     *
     * @return void
     */
    public function setShowOnlyOwnEntries($showOnlyOwnEntries)
    {
        if (boolval($this->showOnlyOwnEntries) !== boolval($showOnlyOwnEntries)) {
            $this->showOnlyOwnEntries = boolval($showOnlyOwnEntries);
        }
    }
    
    /**
     * Returns the enabled finder types.
     *
     * @return string
     */
    public function getEnabledFinderTypes()
    {
        return $this->enabledFinderTypes;
    }
    
    /**
     * Sets the enabled finder types.
     *
     * @param string $enabledFinderTypes
     *
     * @return void
     */
    public function setEnabledFinderTypes($enabledFinderTypes)
    {
        if ($this->enabledFinderTypes !== $enabledFinderTypes) {
            $this->enabledFinderTypes = isset($enabledFinderTypes) ? $enabledFinderTypes : '';
        }
    }
    
    
    /**
     * Loads module variables from the database.
     */
    protected function load()
    {
        $moduleVars = $this->variableApi->getAll('ZikulaContentModule');
    
        if (isset($moduleVars['stateOfNewPages'])) {
            $this->setStateOfNewPages($moduleVars['stateOfNewPages']);
        }
        if (isset($moduleVars['countPageViews'])) {
            $this->setCountPageViews($moduleVars['countPageViews']);
        }
        if (isset($moduleVars['googleMapsApiKey'])) {
            $this->setGoogleMapsApiKey($moduleVars['googleMapsApiKey']);
        }
        if (isset($moduleVars['enableRawPlugin'])) {
            $this->setEnableRawPlugin($moduleVars['enableRawPlugin']);
        }
        if (isset($moduleVars['stylingClasses'])) {
            $this->setStylingClasses($moduleVars['stylingClasses']);
        }
        if (isset($moduleVars['inheritPermissions'])) {
            $this->setInheritPermissions($moduleVars['inheritPermissions']);
        }
        if (isset($moduleVars['enableOptionalString1'])) {
            $this->setEnableOptionalString1($moduleVars['enableOptionalString1']);
        }
        if (isset($moduleVars['enableOptionalString2'])) {
            $this->setEnableOptionalString2($moduleVars['enableOptionalString2']);
        }
        if (isset($moduleVars['enableOptionalText'])) {
            $this->setEnableOptionalText($moduleVars['enableOptionalText']);
        }
        if (isset($moduleVars['pageEntriesPerPage'])) {
            $this->setPageEntriesPerPage($moduleVars['pageEntriesPerPage']);
        }
        if (isset($moduleVars['linkOwnPagesOnAccountPage'])) {
            $this->setLinkOwnPagesOnAccountPage($moduleVars['linkOwnPagesOnAccountPage']);
        }
        if (isset($moduleVars['showOnlyOwnEntries'])) {
            $this->setShowOnlyOwnEntries($moduleVars['showOnlyOwnEntries']);
        }
        if (isset($moduleVars['enabledFinderTypes'])) {
            $this->setEnabledFinderTypes($moduleVars['enabledFinderTypes']);
        }
    }
    
    /**
     * Saves module variables into the database.
     */
    public function save()
    {
        $this->variableApi->set('ZikulaContentModule', 'stateOfNewPages', $this->getStateOfNewPages());
        $this->variableApi->set('ZikulaContentModule', 'countPageViews', $this->getCountPageViews());
        $this->variableApi->set('ZikulaContentModule', 'googleMapsApiKey', $this->getGoogleMapsApiKey());
        $this->variableApi->set('ZikulaContentModule', 'enableRawPlugin', $this->getEnableRawPlugin());
        $this->variableApi->set('ZikulaContentModule', 'stylingClasses', $this->getStylingClasses());
        $this->variableApi->set('ZikulaContentModule', 'inheritPermissions', $this->getInheritPermissions());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalString1', $this->getEnableOptionalString1());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalString2', $this->getEnableOptionalString2());
        $this->variableApi->set('ZikulaContentModule', 'enableOptionalText', $this->getEnableOptionalText());
        $this->variableApi->set('ZikulaContentModule', 'pageEntriesPerPage', $this->getPageEntriesPerPage());
        $this->variableApi->set('ZikulaContentModule', 'linkOwnPagesOnAccountPage', $this->getLinkOwnPagesOnAccountPage());
        $this->variableApi->set('ZikulaContentModule', 'showOnlyOwnEntries', $this->getShowOnlyOwnEntries());
        $this->variableApi->set('ZikulaContentModule', 'enabledFinderTypes', $this->getEnabledFinderTypes());
    }
}
