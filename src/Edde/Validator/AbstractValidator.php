<?php
declare(strict_types=1);

namespace Edde\Validator;

use Edde\SimpleObject;
use function sprintf;

abstract class AbstractValidator extends SimpleObject implements IValidator {
    /**
     * @param array $options
     *
     * @return string
     */
    protected function getValueName(array $options = null): string {
        return $options['name'] ?? '<unknown value name, use name in validator $options to set name>';
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return bool true means value is available (!==null)
     *
     * @throws ValidatorException
     */
    protected function checkRequired($value, array $options = null): bool {
        if ($value === null && ($options['required'] ?? true) === true) {
            throw new ValidatorException(sprintf('Required value [%s] is null.', $this->getValueName($options)));
        }
        return $value !== null;
    }
}
