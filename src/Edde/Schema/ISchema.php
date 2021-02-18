<?php
declare(strict_types=1);

namespace Edde\Schema;

interface ISchema {
    /**
     * return name of a schema (it could have even "namespace" like name)
     *
     * @return string
     */
    public function getName(): string;

    /**
     * return a primary attribute of this schema
     *
     * @return IAttribute
     */
    public function getPrimary(): IAttribute;

    /**
     * has this schema an alias?
     *
     * @return bool
     */
    public function hasAlias(): bool;

    /**
     * get alias of this schema
     *
     * @return string|null
     */
    public function getAlias(): ?string;

    /**
     * return alias or the original schema name
     *
     * @return string
     */
    public function getRealName(): string;

    /**
     * return meta attribute of the schema
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getMeta(string $name, $default = null);

    /**
     * @param string $name
     *
     * @return IAttribute
     *
     * @throws SchemaException
     */
    public function getAttribute(string $name): IAttribute;

    /**
     * return list of attributes of this schema
     *
     * @return IAttribute[]
     */
    public function getAttributes(): array;

    /**
     * return list of unique properties
     *
     * @return IAttribute[]
     */
    public function getUniques(): array;

    /**
     * is this schema relation?
     *
     * @return bool
     */
    public function isRelation(): bool;

    /**
     * get source attribute of relation (source)->(target)
     *
     * @return IAttribute
     *
     * @throws SchemaException
     */
    public function getSource(): IAttribute;

    /**
     * get target attribute of relation (source)->(target)
     *
     * @return IAttribute
     *
     * @throws SchemaException
     */
    public function getTarget(): IAttribute;

    /**
     * @param ISchema $source
     * @param ISchema $target
     *
     * @throws SchemaException
     */
    public function checkRelation(ISchema $source, ISchema $target): void;
}
