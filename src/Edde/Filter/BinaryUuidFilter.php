<?php
declare(strict_types=1);

namespace Edde\Filter;

use function bin2hex;
use function hex2bin;
use function str_replace;

class BinaryUuidFilter extends AbstractFilter {
    /** @inheritdoc */
    public function input($value, ?array $options = null) {
        return hex2bin(str_replace('-', '', $value));
    }

    /** @inheritdoc */
    public function output($value, ?array $options = null) {
        $value = bin2hex($value);
        return implode('-', [
            substr($value, 0, 8),
            substr($value, 8, 4),
            substr($value, 12, 4),
            substr($value, 16, 4),
            substr($value, 20),
        ]);
    }
}
