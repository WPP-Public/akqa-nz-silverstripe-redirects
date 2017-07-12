<?php

namespace Heyday\SilverStripeRedirects\Code;

use SilverStripe\Forms\Validator;

class RedirectUrlValidator extends Validator
{
    /**
     * @param array $data
     * @return boolean
     */
    public function php($data)
    {
        $valid = true;

        if ($data['FromType'] == 'page' && $data['FromRelationID'] == 0) {
            $this->validationError(
                'FromRelationID',
                "A 'From' page must be specified",
                "required"
            );
            $valid = false;
        }
        
        if ($data['FromType'] == 'manual' && empty($data['From'])) {
            $this->validationError(
                'From',
                "A 'From' url must be specified",
                "required"
            );
            $valid = false;
        }
        
        if ($data['ToType'] == 'page' && $data['ToRelationID'] == 0) {
            $this->validationError(
                'ToRelationID',
                "A 'To' page must be specified",
                "required"
            );
            $valid = false;
        }
        
        if ($data['ToType'] == 'manual' && empty($data['To'])) {
            $this->validationError(
                'To',
                "A 'To' url must be specified",
                "required"
            );
            $valid = false;
        }



        return $valid;


    }
}
