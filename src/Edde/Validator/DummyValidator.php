<?php
declare(strict_types=1);

namespace Edde\Validator;

class DummyValidator extends AbstractValidator {
    /** @inheritdoc */
    public function validate($value, array $options = null): void {
    }
}
