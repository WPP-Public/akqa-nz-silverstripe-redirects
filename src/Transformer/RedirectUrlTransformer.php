<?php

namespace Heyday\Redirects\Transformer;

use Heyday\Redirects\Redirect;
use RedirectUrl;
use Heyday\Redirects\TransformerInterface;

class RedirectUrlTransformer implements TransformerInterface
{
    /**
     * @param mixed $item
     * @return array
     */
    public function transform($item)
    {
        if ($item instanceof RedirectUrl) {
            return new Redirect($item->getFromLink(), $item->getToLink(), $item->getStatusCode());
        } else {
            throw new \InvalidArgumentException(sprintf(
                "Instance provided to %s::%s must be an instance of RedirectUrl '%s' given",
                __CLASS__,
                __FUNCTION__,
                is_object($item) ? get_class($item) : gettype($item)
            ));
        }
    }
}