<?php

namespace Heyday\SilverStripeRedirects\Source;

/**
 * @package Heyday\SilverStripeRedirects\Source
 */
interface CacheableDataSourceInterface extends DataSourceInterface
{
    /**
     * @return string
     */
    public function getKey();
}