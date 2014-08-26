<?php

namespace Heyday\Redirects\DataSource;

use Heyday\Redirects\CacheableDataSourceInterface;
use Heyday\Redirects\DataSourceInterface;

class CachedDataSource implements DataSourceInterface
{
    /** @var \Heyday\Redirects\CacheableDataSourceInterface */
    protected $dataSource;
    
    /** @var \Doctrine\Common\Cache\CacheProvider */
    protected $cache;

    /**
     * @param \Heyday\Redirects\CacheableDataSourceInterface $dataSource
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     */
    public function __construct(CacheableDataSourceInterface $dataSource, $cache)
    {
        $this->dataSource = $dataSource;
        $this->cache = $cache;
    }

    /**
     * @return \Heyday\Redirects\Redirect[]
     */
    public function get()
    {
        $key = $this->dataSource->getKey();

        if (!$result = $this->cache->fetch($key)) {
            $this->cache->save(
                $key,
                $result = $this->dataSource->get()
            );
        }

        return $result;
    }

    /**
     * Delete the cached version
     */
    public function delete()
    {
        $this->cache->delete($this->dataSource->getKey());
    }
}