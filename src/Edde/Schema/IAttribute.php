<?php
declare(strict_types=1);

namespace Edde\Schema;

/**
 * An attribute is "property" of schema which is basically
 * definition of a "real" property describing it's metadata.
 */
interface IAttribute {
    /**
     * get attribute name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * get type of an attribute
     *
     * @return string
     */
    public function getType(): string;

    /**
     * is this property marked as primary?
     *
     * @return bool
     */
    public function isPrimary(): bool;

    /**
     * is this property marked as unique?
     *
     * @return bool
     */
    public function isUnique(): bool;

    /**
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * return name of the validator for this property, if any
     *
     * @return null|string
     */
    public function getValidator(): ?string;

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * get a filter with the given name
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getFilter(string $name): ?string;

    /**
     * is this property reference to another schema?
     *
     * @return bool
     */
    public function hasSchema(): bool;

    /**
     * return target schema or thrown an exception if not available
     *
     * @return string
     *
     * @throws SchemaException
     */
    public function getSchema(): string;
}
