<?php
declare(strict_types=1);

namespace Edde\Filter;

use DateTime;

class DateTimeFilter extends AbstractFilter {
    /** @inheritdoc */
    public function input($value, ?array $options = null) {
        if ($value instanceof DateTime) {
            return $value;
        } else if ($value) {
            return new DateTime($value);
        }
        return null;
    }

    /** @inheritdoc */
    public function output($value, ?array $options = null) {
        if ($value && $value instanceof DateTime === false) {
            $value = new DateTime($value);
        }
        return $value ? $value->format('Y-m-d\TH:i:s.u') : null;
    }
}
