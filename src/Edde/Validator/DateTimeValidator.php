<?php
declare(strict_types=1);

namespace Edde\Validator;

use DateTime;

class DateTimeValidator extends AbstractValidator {
    /** @inheritdoc */
    public function validate($value, array $options = null): void {
        if ($this->checkRequired($value, $options) && $value instanceof DateTime === false) {
            throw new ValidatorException(sprintf('Value [%s] is not instanceof DateTime.', $this->getValueName($options)));
        }
    }
}
