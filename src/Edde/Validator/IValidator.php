<?php
declare(strict_types=1);

namespace Edde\Validator;

interface IValidator {
    /**
     * validate the given input
     *
     * @param mixed $value
     * @param array $options
     *
     * @throws ValidatorException
     */
    public function validate($value, array $options = null): void;
}
