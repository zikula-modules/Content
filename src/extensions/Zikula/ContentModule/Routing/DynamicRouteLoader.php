<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Dynamic route loader.
 */
class DynamicRouteLoader extends Loader
{
    /**
     * @var bool
     */
    protected $ignoreBundleNameInRoutes;

    /**
     * @var bool
     */
    protected $ignoreEntityNameInRoutes;

    /**
     * @var bool
     */
    protected $ignoreFirstTreeLevelInRoutes;

    /**
     * @var string
     */
    protected $permalinkSuffix;

    /**
     * @var bool
     */
    protected $isLoaded;

    public function __construct(
        VariableApiInterface $variableApi
    ) {
        $this->ignoreBundleNameInRoutes = (bool) $variableApi->get(
            'ZikulaContentModule',
            'ignoreBundleNameInRoutes',
            true
        );
        $this->ignoreEntityNameInRoutes = (bool) $variableApi->get(
            'ZikulaContentModule',
            'ignoreEntityNameInRoutes',
            true
        );
        $this->ignoreFirstTreeLevelInRoutes = (bool) $variableApi->get(
            'ZikulaContentModule',
            'ignoreFirstTreeLevelInRoutes',
            true
        );
        $this->permalinkSuffix = $variableApi->get(
            'ZikulaContentModule',
            'permalinkSuffix',
            'none'
        );
        $this->isLoaded = false;
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        if (true === $this->isLoaded) {
            //throw new \RuntimeException('Do not add the "content_dynamic" loader twice');
            return $routes;
        }

        $routeVariants = [
            'admin' => ['pathPrefix' => '/admin', 'controller' => 'adminDisplay'],
            'user' => ['pathPrefix' => '', 'controller' => 'display'],
        ];
        $entityPathSegment = true === $this->ignoreEntityNameInRoutes ? '' : '/page';

        foreach ($routeVariants as $variantName => $variantData) {
            $path = $variantData['pathPrefix'] . $entityPathSegment . '/{slug}.{_format}';

            $defaults = [
                '_controller' => 'Zikula\\ContentModule\\Controller\\PageController::' . $variantData['controller'],
                'ignoreFirstTreeLevelInRoutes' => $this->ignoreFirstTreeLevelInRoutes,
                'permalinkSuffix' => $this->permalinkSuffix,
            ];
            $requiredFormat = $this->permalinkSuffix;
            if ('none' === $this->permalinkSuffix) {
                $defaults['_format'] = 'html';
                $requiredFormat = 'html';
            }
            $requirements = [
                'slug' => '[^.]+',
                '_format' => $requiredFormat . '|xml|json|ics|pdf',
            ];
            $options = [
                'i18n' => true, // needs to be enabled for locale prefix
                                // BUT we need to remove zikulacontentmodule_page_admindisplay
                                // and zikulacontentmodule_page_display from routes.en.po
                                // to allow dynamic changes of the corresponding pathes
                'expose' => true,
                'zkPosition' => 'bottom',
            ];
            if (true === $this->ignoreBundleNameInRoutes) {
                $options['zkNoBundlePrefix'] = true;
            }
            $host = '';
            $schemes = [];
            $methods = ['GET'];
            $condition = ''; // see https://symfony.com/doc/current/routing/conditions.html
            $route = new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);

            $routes->add($variantName . 'Display', $route);
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'content_dynamic' === $type;
    }
}
