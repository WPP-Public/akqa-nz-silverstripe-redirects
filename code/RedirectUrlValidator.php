<?php

class RedirectUrlValidator extends Validator
{
    public function php($data)
    {
        $valid = true;

        if (empty($data['From']) && empty($data['FromRelationID'])) {
            foreach (['From', 'FromRelationID'] as $error) {
                $this->validationError(
                    $error,
                    "A 'From' url must be specified",
                    "required"
                );
            }
            $valid = false;
        }

        if (empty($data['To']) && empty($data['ToRelationID'])) {
            foreach (['To', 'ToRelationID'] as $error) {
                $this->validationError(
                    $error,
                    "A 'To' url must be specified",
                    "required"
                );
            }
            $valid = false;
        }
        
        return $valid;
    }
}