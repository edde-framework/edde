<?php
declare(strict_types=1);

namespace Edde\Http;

use IteratorAggregate;

/**
 * Explicit interface for http header list.
 */
interface IHeaders extends IteratorAggregate {
    /**
     * set exactly the value to the header; it replaces the original value (even if it was more
     * values of the same header)
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return IHeaders
     */
    public function set(string $name, $value): IHeaders;

    /**
     * add a header; value could be even prepared helper object (like IContentType, ...)
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return IHeaders
     */
    public function add(string $name, $value): IHeaders;

    /**
     * add an array of headers (add not set); when there is an name with array of values,
     * it's added as more headers of the same name (for example multiple Set-Cookie)
     *
     * @param array $headers
     *
     * @return IHeaders
     */
    public function put(array $headers): IHeaders;

    /**
     * is the given header name present?
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * return the given header; header could be basically everything - from utility object to an
     * array with multiple values
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * return a simple array with headers; if there is more values per one headers,
     * they would added to sub array
     *
     * @return array
     */
    public function toArray(): array;
}
