<?php
declare(strict_types=1);

namespace Edde\Node;

use IteratorAggregate;

interface IAttributes extends IteratorAggregate {
    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttributes(string $name): bool;
}
