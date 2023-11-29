<?php

namespace Heyday\SilverStripeRedirects\Source;

use SilverStripe\ORM\DataExtension;

class RedirectExtension extends DataExtension
{
    public function onAfterDelete()
    {
        $redirects = RedirectUrl::get()->filterAny([
            'FromRelationID' => $this->owner->ID,
            'ToRelationID' => $this->owner->ID
        ]);

        foreach ($redirects as $redirect) {
            $redirect->delete();
        }
    }
}
