<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\PhpFileCache;

class PhpFileCacheTest extends CacheTest
{
    /**
     * @var string
     */
    private $cacheDir;

    public function setup()
    {
        $this->cacheDir = __DIR__.'/cache_test';

        $this->cleanupCacheDir();
        mkdir($this->cacheDir);
    }

    protected function _getCacheDriver()
    {
        $cache = new PhpFileCache();

        $cache->setPath($this->cacheDir);

        return $cache;
    }

    public function tearDown()
    {
        $this->cleanupCacheDir();
    }

    private function cleanupCacheDir()
    {
        if (is_dir($this->cacheDir)) {
            foreach(glob($this->cacheDir . '/*') as $file) {
                unlink($file);
            }

            rmdir($this->cacheDir);
        }
    }
}
