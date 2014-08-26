<?php

namespace Heyday\Redirects\DataSource;

use Heyday\Redirects\DataSourceInterface;

class GroupedDataSource implements DataSourceInterface
{
    /**
     * @var \Heyday\Redirects\DataSourceInterface[]
     */
    protected $dataSources = [];

    /**
     * @param \Heyday\Redirects\DataSourceInterface[] $dataSources
     */
    public function __construct(array $dataSources)
    {
        $this->dataSources = $dataSources;
    }

    /**
     * @return \Heyday\Redirects\Redirect[]
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