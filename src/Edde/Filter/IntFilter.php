<?php
declare(strict_types=1);

namespace Edde\Filter;

use const FILTER_VALIDATE_INT;

class IntFilter extends AbstractFilter {
    /** @inheritdoc */
    public function input($value, ?array $options = null) {
        return (int)filter_var($value, FILTER_VALIDATE_INT);
    }

    /** @inheritdoc */
    public function output($value, ?array $options = null) {
        return $this->input($value);
    }
}
