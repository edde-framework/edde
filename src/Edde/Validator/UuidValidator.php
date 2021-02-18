<?php
declare(strict_types=1);

namespace Edde\Validator;

use function is_string;
use function preg_match;

class UuidValidator extends AbstractValidator {
    /** @inheritdoc */
    public function validate($value, array $options = null): void {
        if ($this->checkRequired($value, $options) === false) {
            return;
        }
        if (is_string($value) === false) {
            throw new ValidatorException(sprintf('Value [%s] is not string.', $this->getValueName($options)));
        }
        if (preg_match('~[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-(8|9|a|b)[a-f0-9]{3}-[a-f0-9]{12}~', $value) !== 1) {
            throw new ValidatorException(sprintf('Value [%s] [%s] is not valid uuid v4.', $value, $this->getValueName($options)));
        }
    }
}
