<?php

namespace Heyday\SilverStripeRedirects\Source\DataSource;

use Heyday\SilverStripeRedirects\Source\DataSourceInterface;
use Heyday\SilverStripeRedirects\Source\CacheableDataSourceInterface;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;

class CachedDataSource implements DataSourceInterface
{
    /** @var \Heyday\SilverStripeRedirects\Source\CacheableDataSourceInterface */
    protected $dataSource;

    /** @var CacheInterface */
    protected $cache;

    /**
     * @param CacheableDataSourceInterface $dataSource
     */
    public function __construct(CacheableDataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->cache = Injector::inst()->get(CacheInterface::class . '.redirectsCache');
    }

    /**
     * @return \Heyday\SilverStripeRedirects\Source\Redirect[]
     */
    public function get()
    {
        $key = $this->dataSource->getKey();

        if (!$this->cache->has($key)) {
            $this->cache->set(
                $key,
                $result = $this->dataSource->get()
            );
        } else {
            $result = $this->cache->get($key);
        }

        return $result;
    }

    /**
     * Delete the cached version
     * @return void
     */
    public function delete()
    {
        $this->cache->clear();
    }
}
