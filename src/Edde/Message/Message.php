<?php
declare(strict_types=1);

namespace Edde\Message;

use Edde\SimpleObject;
use stdClass;

class Message extends SimpleObject implements IMessage {
    /** @var string */
    protected $type;
    /** @var string|null */
    protected $target;
    /** @var array */
    protected $attrs;

    /**
     * @param string $type
     * @param string $target
     * @param array  $attrs
     */
    public function __construct(string $type, string $target = null, array $attrs = null) {
        $this->type = $type;
        $this->target = $target;
        $this->attrs = $attrs;
    }

    /** @inheritdoc */
    public function getType(): string {
        return $this->type;
    }

    /** @inheritdoc */
    public function getTarget(): ?string {
        return $this->target;
    }

    /** @inheritdoc */
    public function getAttrs(): ?array {
        return $this->attrs;
    }

    /** @inheritdoc */
    public function export(): stdClass {
        return (object)[
            'type'   => $this->type,
            'target' => $this->target,
            'attrs'  => $this->attrs ? (object)$this->attrs : null,
        ];
    }

    /** @inheritdoc */
    public function offsetExists($offset) {
        return isset($this->attrs[$offset]) || array_key_exists($offset, $this->attrs);
    }

    /** @inheritdoc */
    public function offsetGet($offset) {
        return $this->attrs[$offset];
    }

    /** @inheritdoc */
    public function offsetSet($offset, $value) {
        $this->attrs[$offset] = $value;
    }

    /** @inheritdoc */
    public function offsetUnset($offset) {
        unset($this->attrs[$offset]);
    }
}
