<?php
declare(strict_types=1);

namespace Edde\Storage;

use Edde\SimpleObject;
use function array_key_exists;
use function array_merge;

class Entity extends SimpleObject implements IEntity {
    /** @var string */
    protected $schema;
    /** @var array */
    protected $source;

    /**
     * @param string $schema
     * @param array  $source
     */
    public function __construct(string $schema, array $source) {
        $this->schema = $schema;
        $this->source = $source;
    }

    /** @inheritdoc */
    public function getSchema(): string {
        return $this->schema;
    }

    /** @inheritdoc */
    public function put(array $put): IEntity {
        $this->source = array_merge($this->source, $put);
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): array {
        return $this->source;
    }

    /** @inheritdoc */
    public function offsetExists($offset) {
        return isset($this->source[$offset]) || array_key_exists($offset, $this->source);
    }

    /** @inheritdoc */
    public function offsetGet($offset) {
        return $this->source[$offset];
    }

    /** @inheritdoc */
    public function offsetSet($offset, $value) {
        $this->source[$offset] = $value;
    }

    /** @inheritdoc */
    public function offsetUnset($offset) {
        unset($this->source[$offset]);
    }
}
