<?php

namespace Heyday\Redirects;

/**
 * @package Heyday\Redirects
 */
interface CacheableDataSourceInterface extends DataSourceInterface
{
    /**
     * @return string
     */
    public function getKey();
}