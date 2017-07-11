<?php

namespace Heyday\SilverStripeRedirects\Source;

interface TransformerInterface
{
    /**
     * @param mixed $item
     * @return \Heyday\SilverStripeRedirects\Source\Redirect
     */
    public function transform($item);
}