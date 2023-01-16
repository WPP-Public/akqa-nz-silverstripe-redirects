<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\ORM\DataExtension;

class RedirectExtension extends DataExtension
{
    public function onAfterDelete()
    {
        $redirects = RedirectUrl::get()->filterAny([
            'FromID' => $this->owner->ID,
            'ToID' => $this->owner->ID
        ]);

        foreach ($redirects as $redirect) {
            $redirect->delete();
        }
    }
}
