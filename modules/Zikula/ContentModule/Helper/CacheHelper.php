<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper;

use DateInterval;
use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Helper class for caching external content.
 */
class CacheHelper
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $cacheDirectory;

    /**
     * @var int
     */
    protected $lifeTime;

    public function __construct(
        Filesystem $filesystem,
        $cacheDirectory
    ) {
        $this->filesystem = $filesystem;
        $this->cacheDirectory = $cacheDirectory;

        if (!$this->filesystem->exists($this->cacheDirectory)) {
            try {
                $this->filesystem->mkdir($this->cacheDirectory, 0777);
            } catch (IOExceptionInterface $exception) {
                // ignore (for now)
            }
        }

        $this->setLifeTime(2);
    }

    /**
     * Loads a data file either from cache or from external source.
     */
    public function fetch(string $url = ''): string
    {
        $hasCache = $this->filesystem->exists($this->cacheDirectory);

        $cacheFile = md5($url);
        $cacheFile = $this->cacheDirectory . $cacheFile;

        $refetch = false;
        if ($hasCache && !$this->filesystem->exists($cacheFile)) {
            $refetch = true;
        } else {
            $compareDate = date('Y-m-d H:i:s', filectime($cacheFile));
            $thresholdDate = new DateTime();
            $thresholdDate->sub(new DateInterval($this->getLifetime()));
            $thresholdDate = $thresholdDate->format('Y-m-d H:i:s');
            if ($compareDate < $thresholdDate) {
                unlink($cacheFile);
                $refetch = true;
            }
        }

        if (true !== $refetch) {
            return file_get_contents($cacheFile);
        }

        // fetch from source
        $client = new Client();
        $response = $client->get($url);
        $result = '';
        if (200 === $response->getStatusCode()) {
            $result = (string) $response->getBody();
        }

        if ($hasCache) {
            file_put_contents($cacheFile, $result);
        }

        return $result;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    /**
     * Sets the lifetime of cache files.
     */
    public function setLifeTime(int $hours): void
    {
        $this->lifeTime = $hours;
    }

    /**
     * Returns the lifetime of cache files.
     */
    public function getLifetime(): string
    {
        return 'PT' . $this->lifeTime . 'H';
    }
}