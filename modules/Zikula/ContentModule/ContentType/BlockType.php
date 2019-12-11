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

namespace Zikula\ContentModule\ContentType;

use RuntimeException;
use Symfony\Component\Routing\RouterInterface;
use Zikula\BlocksModule\Api\ApiInterface\BlockApiInterface;
use Zikula\BlocksModule\Entity\BlockEntity;
use Zikula\BlocksModule\Entity\RepositoryInterface\BlockRepositoryInterface;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Common\Content\AbstractContentType;
use Zikula\ContentModule\ContentType\Form\Type\BlockType as FormType;
use Zikula\ThemeModule\Engine\Engine;

/**
 * Block content type.
 */
class BlockType extends AbstractContentType
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var BlockApiInterface
     */
    protected $blockApi;

    /**
     * @var Engine
     */
    protected $themeEngine;

    public function getIcon(): string
    {
        return 'cubes';
    }

    public function getTitle(): string
    {
        return $this->__('Block');
    }

    public function getDescription(): string
    {
        return $this->__('Display Zikula blocks.');
    }

    public function getDefaultData(): array
    {
        return [
            'blockId' => 0
        ];
    }

    public function displayView(): string
    {
        $this->fetchBlock();

        return parent::displayView();
    }

    public function displayEditing(): string
    {
        $output = $this->displayView();

        if ('' === $this->data['content'] && '' !== $this->data['noDisplayMessage']) {
            return '<p class="alert alert-info">' . $this->data['noDisplayMessage'] . '</p>';
        }

        /** @var BlockEntity $block */
        $block = $this->data['block'];
        $quickActions = '';
        $quickActions .= '<a href="javascript:void(0);" title="'
            . $this->translator->__('Preview block content')
            . '" onclick="'
            . 'jQuery(this).parent().next(\'.hidden\').removeClass(\'hidden\'); '
            . 'jQuery(this).remove();'
            . '"><i class="fa fa-2x fa-eye"></i></a>'
        ;
        $editLink = $this->router->generate(
            'zikulablocksmodule_block_edit',
            ['blockEntity' => $this->data['blockId']]
        );
        $quickActions .= ' <a href="' . $editLink . '"'
            . ' title="' . $this->translator->__('Edit this block') . '"'
            . ' target="_blank"><i class="fa fa-2x fa-pencil-square-o"></i></a>'
        ;
        $editOutput = '<h3>' . $block->getTitle() . '</h3>';
        if ($block->getDescription()) {
            $editOutput .= '<p><em>' . $block->getDescription() . '</em></p>';
        }
        $editOutput .= '<p>' . $quickActions . '</p>';
        $editOutput .= '<div class="hidden">' . $output . '</div>';

        return $editOutput;
    }

    /**
     * Retrieves block information.
     */
    protected function fetchBlock(): void
    {
        $this->data['block'] = null;
        $this->data['content'] = '';
        $this->data['noDisplayMessage'] = '';
        if ($this->data['blockId'] < 1) {
            return;
        }

        /** @var BlockEntity $block */
        $block = $this->data['block'] = $this->blockRepository->find($this->data['blockId']);
        if (null === $block) {
            return;
        }

        // Check if providing module is available and if block is active.
        $bundleName = $block->getModule()->getName();
        $moduleInstance = $this->kernel->getModule($bundleName);
        if (!isset($moduleInstance)) {
            $this->data['noDisplayMessage'] = $this->translator->__f(
                'Module %module is not available.',
                ['%module' => $bundleName]
            );

            return;
        }
        if (!$block->getActive()) {
            $this->data['noDisplayMessage'] = $this->translator->__('Block is inactive.');

            return;
        }

        // copied from Zikula\BlocksModule\Twig\Extension\BlocksExtension:

        // add theme path to twig loader for theme overrides using namespace notation (e.g. @BundleName/foo)
        // this duplicates functionality from \Zikula\ThemeModule\EventListener\TemplatePathOverrideListener::setUpThemePathOverrides
        // but because blockHandlers don't call (and are not considered) a controller, that listener doesn't get called.
        $theme = $this->themeEngine->getTheme();
        if ($theme) {
            $overridePath = $theme->getPath() . '/Resources/' . $bundleName . '/views';
            if (is_readable($overridePath)) {
                $paths = $this->twigLoader->getPaths($bundleName);
                // inject themeOverridePath before the original path in the array
                array_splice($paths, count($paths) - 1, 0, [$overridePath]);
                $this->twigLoader->setPaths($paths, $bundleName);
            }
        }

        try {
            $blockInstance = $this->blockApi->createInstanceFromBKey($block->getBkey());
        } catch (RuntimeException $exception) {
            return;
        }

        $positionName = 'contentblock';
        $blockProperties = $block->getProperties();
        $blockProperties['bid'] = $block->getBid();
        $blockProperties['title'] = $block->getTitle();
        $blockProperties['position'] = $positionName;
        $content = $blockInstance->display($blockProperties);
        if (isset($moduleInstance)) {
            // add module stylesheet to page
            $moduleInstance->addStylesheet();
        }

        $this->data['content'] = $this->themeEngine->wrapBlockContentInTheme($content, $block->getTitle(), $block->getBlocktype(), $block->getBid(), $positionName);
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        ZikulaHttpKernelInterface $kernel,
        RouterInterface $router,
        BlockRepositoryInterface $blockRepository,
        BlockApiInterface $blockApi,
        Engine $themeEngine
    ): void {
        $this->kernel = $kernel;
        $this->router = $router;
        $this->blockRepository = $blockRepository;
        $this->blockApi = $blockApi;
        $this->themeEngine = $themeEngine;
    }
}
