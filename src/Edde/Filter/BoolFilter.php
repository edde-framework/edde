<?php
declare(strict_types=1);

namespace Edde\Filter;

/**
 * Both sides (input/output) ensures boolean.
 */
class BoolFilter extends AbstractFilter {
    /** @inheritdoc */
    public function input($value, ?array $options = null) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /** @inheritdoc */
    public function output($value, ?array $options = null) {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
