<?php

namespace Heyday\SilverStripeRedirects\Source;

interface DataSourceInterface
{
    /**
     * @return \Heyday\SilverStripeRedirects\Source\Redirect[]
     */
    public function get();
}