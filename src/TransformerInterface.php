<?php

namespace Heyday\Redirects;

interface TransformerInterface
{
    /**
     * @param mixed $item
     * @return \Heyday\Redirects\Redirect
     */
    public function transform($item);
}