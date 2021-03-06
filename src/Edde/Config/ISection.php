<?php
declare(strict_types=1);

namespace Edde\Config;

use stdClass;

interface ISection {
    /**
     * return section name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * require the given value
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws ConfigException
     */
    public function require(string $name);

    /**
     * try to get a value or return default
     *
     * @param string $name
     * @param        $default
     *
     * @return mixed
     */
    public function optional(string $name, $default = null);

    /**
     * return section as an object
     *
     * @return stdClass
     */
    public function toObject(): stdClass;
}
