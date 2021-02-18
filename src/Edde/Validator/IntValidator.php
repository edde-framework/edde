<?php
declare(strict_types=1);

namespace Edde\Validator;

use function is_int;

class IntValidator extends AbstractValidator {
    /** @inheritdoc */
    public function validate($value, array $options = null): void {
        if ($this->checkRequired($value, $options) && is_int($value) === false) {
            throw new ValidatorException(sprintf('Value [%s] is not integer.', $this->getValueName($options)));
        }
    }
}
