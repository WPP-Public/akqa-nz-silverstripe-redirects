<?php

namespace Heyday\Redirects;

interface DataSourceInterface
{
    /**
     * @return \Heyday\Redirects\Redirect[]
     */
    public function get();
}