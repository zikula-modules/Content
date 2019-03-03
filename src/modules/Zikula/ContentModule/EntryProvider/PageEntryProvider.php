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

namespace Zikula\ContentModule\EntryProvider;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;

/**
 * Page entry provider.
 */
class PageEntryProvider
{
    /**
     * Translator instance
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * Bundle name
     *
     * @var string
     */
    private $bundleName;

    /**
     * The name of this provider
     *
     * @var string
     */
    private $name;

    /**
     * Whether automatic page linking is enabled or not
     *
     * @var boolean
     */
    private $enableAutomaticPageLinks;

    /**
     * PageEntryProvider constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param EntityFactory $entityFactory
     * @param boolean $enableAutomaticPageLinks
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        EntityFactory $entityFactory,
        $enableAutomaticPageLinks
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->entityFactory = $entityFactory;
        $this->enableAutomaticPageLinks = $enableAutomaticPageLinks;

        $nsParts = explode('\\', get_class($this));
        $vendor = $nsParts[0];
        $nameAndType = $nsParts[1];

        $this->bundleName = $vendor . $nameAndType;
        $this->name = str_replace('Type', '', array_pop($nsParts));
    }

    /**
     * Returns the bundle name.
     *
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * Returns the name of this entry provider.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the icon name (FontAwesome icon code suffix, e.g. "pencil").
     *
     * @return string
     */
    public function getIcon()
    {
        return 'book';
    }

    /**
     * Returns the title of this entry provider.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->translator->__('Pages', 'zikulacontentmodule');
    }

    /**
     * Returns the description of this entry provider.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->translator->__('Links page titles to corresponding pages.', 'zikulacontentmodule');
    }

    /**
     * Returns an extended plugin information shown on settings page.
     *
     * @return string
     */
    public function getAdminInfo()
    {
        return '';
    }

    /**
     * Returns whether this entry provider is active or not.
     *
     * @return boolean
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Returns entries for given entry types.
     *
     * @param string[] $entryTypes
     * @return array
     */
    public function getEntries(array $entryTypes = [])
    {
        $result = [];
        if (true !== $this->enableAutomaticPageLinks) {
            return $result;
        }

        if (!in_array('link', $entryTypes)) {
            return $result;
        }

        $entities = $this->entityFactory->getRepository('page')
            ->selectWhere('', '', false, true);

        foreach ($entities as $entity) {
            $result[] = [
                'longform' => $this->router->generate('zikulacontentmodule_page_display', ['slug' => $entity['slug']], UrlGeneratorInterface::ABSOLUTE_URL),
                'shortform' => $entity['title'],
                'title' => $entity['title'],
                'type' => 'link',
                'language' => ''//$entity->getLocale()
            ];
        }

        return $result;
    }
}
