<?php

namespace Heyday\RSilverStripeRedirects\Source\DataSource;

use Heyday\SilverStripeRedirects\Source\DataSourceInterface;
use Heyday\SilverStripeRedirects\Source\Redirect;

class GroupedDataSource implements DataSourceInterface
{
    /**
     * @var DataSourceInterface[]
     */
    protected $dataSources = [];

    /**
     * @param DataSourceInterface[] $dataSources
     */
    public function __construct(array $dataSources)
    {
        $this->dataSources = $dataSources;
    }

    /**
     * @return Redirect[]
     */
    public function get()
    {
        $redirects = [];
        
        foreach ($this->dataSources as $dataSource) {
            $redirects[] = $dataSource->get();
        }
        
        return call_user_func_array('array_merge', $redirects);
    }
}