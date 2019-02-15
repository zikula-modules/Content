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

namespace Zikula\ContentModule\Base;

use Doctrine\DBAL\Connection;
use RuntimeException;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;

/**
 * Installer base class.
 */
abstract class AbstractContentModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the ZikulaContentModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        $logger = $this->container->get('logger');
        $userName = $this->container->get('zikula_users_module.current_user')->get('uname');
    
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->listEntityClasses());
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('stateOfNewPages', '1');
        $this->setVar('countPageViews', false);
        $this->setVar('googleMapsApiKey', '');
        $this->setVar('yandexTranslateApiKey', '');
        $this->setVar('enableRawPlugin', false);
        $this->setVar('inheritPermissions', false);
        $this->setVar('pageStyles', 'dummy|Dummy');
        $this->setVar('sectionStyles', 'dummy|Dummy');
        $this->setVar('contentStyles', 'dummy|Dummy');
        $this->setVar('enableOptionalString1', false);
        $this->setVar('enableOptionalString2', false);
        $this->setVar('enableOptionalText', false);
        $this->setVar('ignoreBundleNameInRoutes', true);
        $this->setVar('ignoreEntityNameInRoutes', true);
        $this->setVar('ignoreFirstTreeLevelInRoutes', true);
        $this->setVar('permalinkSuffix', 'none');
        $this->setVar('pageEntriesPerPage', 10);
        $this->setVar('linkOwnPagesOnAccountPage', true);
        $this->setVar('pagePrivateMode', false);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('allowModerationSpecificCreatorForPage', false);
        $this->setVar('allowModerationSpecificCreationDateForPage', false);
        $this->setVar('enabledFinderTypes', 'page');
        $this->setVar('revisionHandlingForPage', 'unlimited');
        $this->setVar('maximumAmountOfPageRevisions', '25');
        $this->setVar('periodForPageRevisions', 'P1Y0M0DT0H0M0S');
        $this->setVar('showPageHistory', true);
    
        // add default entry for category registry (property named Main)
        $categoryHelper = new \Zikula\ContentModule\Helper\CategoryHelper(
            $this->container->get('translator.default'),
            $this->container->get('request_stack'),
            $logger,
            $this->container->get('zikula_users_module.current_user'),
            $this->container->get('zikula_categories_module.category_registry_repository'),
            $this->container->get('zikula_categories_module.api.category_permission')
        );
        $categoryGlobal = $this->container->get('zikula_categories_module.category_repository')->findOneBy(['name' => 'Global']);
        if ($categoryGlobal) {
            $categoryRegistryIdsPerEntity = [];
    
            $registry = new CategoryRegistryEntity();
            $registry->setModname('ZikulaContentModule');
            $registry->setEntityname('PageEntity');
            $registry->setProperty($categoryHelper->getPrimaryProperty('Page'));
            $registry->setCategory($categoryGlobal);
    
            try {
                $this->entityManager->persist($registry);
                $this->entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash('warning', $this->__f('Error! Could not create a category registry for the %entity% entity. If you want to use categorisation, register at least one registry in the Categories administration.', ['%entity%' => 'page']));
                $logger->error('{app}: User {user} could not create a category registry for {entities} during installation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'user' => $userName, 'entities' => 'pages', 'errorMessage' => $exception->getMessage()]);
            }
            $categoryRegistryIdsPerEntity['page'] = $registry->getId();
        }
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the ZikulaContentModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    /**
     * Uninstall ZikulaContentModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->listEntityClasses());
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // remove all module vars
        $this->delVars();
    
        // remove category registry entries
        $registries = $this->container->get('zikula_categories_module.category_registry_repository')->findBy(['modname' => 'ZikulaContentModule']);
        foreach ($registries as $registry) {
            $this->entityManager->remove($registry);
        }
        $this->entityManager->flush();
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for ZikulaContentModule.
     *
     * @return string[] List of class names
     */
    protected function listEntityClasses()
    {
        $classNames = [];
        $classNames[] = 'Zikula\ContentModule\Entity\PageEntity';
        $classNames[] = 'Zikula\ContentModule\Entity\PageLogEntryEntity';
        $classNames[] = 'Zikula\ContentModule\Entity\PageTranslationEntity';
        $classNames[] = 'Zikula\ContentModule\Entity\PageCategoryEntity';
        $classNames[] = 'Zikula\ContentModule\Entity\ContentItemEntity';
        $classNames[] = 'Zikula\ContentModule\Entity\ContentItemTranslationEntity';
    
        return $classNames;
    }
}
