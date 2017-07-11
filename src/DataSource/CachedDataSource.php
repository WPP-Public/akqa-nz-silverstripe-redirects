<?php

namespace Heyday\SilverStripeRedirects\Source\DataSource;


use Heyday\SilverStripeRedirects\Source\DataSourceInterface;
use Heyday\SilverStripeRedirects\Source\CacheableDataSourceInterface;

class CachedDataSource implements DataSourceInterface
{
    /** @var \Heyday\SilverStripeRedirects\Source\CacheableDataSourceInterface */
    protected $dataSource;
    
    /** @var \Doctrine\Common\Cache\CacheProvider */
    protected $cache;

    /**
     * @param CacheableDataSourceInterface $dataSource
     * @param \Doctrine\Common\Cache\CacheProvider $cache
     */
    public function __construct(CacheableDataSourceInterface $dataSource, $cache)
    {
        $this->dataSource = $dataSource;
        $this->cache = $cache;
    }

    /**
     * @return \Heyday\SilverStripeRedirects\Source\Redirect[]
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
     * @return void
     */
    public function delete()
    {
        $this->cache->delete($this->dataSource->getKey());
    }
}