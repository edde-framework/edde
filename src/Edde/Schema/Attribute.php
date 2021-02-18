<?php
declare(strict_types=1);

namespace Edde\Schema;

use Edde\SimpleObject;
use stdClass;
use function property_exists;

class Attribute extends SimpleObject implements IAttribute {
    /** @var stdClass */
    protected $source;

    public function __construct(stdClass $source) {
        $this->source = $source;
    }

    /** @inheritdoc */
    public function getName(): string {
        return (string)$this->source->name;
    }

    /** @inheritdoc */
    public function getType(): string {
        return (string)($this->source->type ?? 'string');
    }

    /** @inheritdoc */
    public function isPrimary(): bool {
        return (bool)($this->source->primary ?? false);
    }

    /** @inheritdoc */
    public function isUnique(): bool {
        return (bool)($this->source->unique ?? false);
    }

    /** @inheritdoc */
    public function isRequired(): bool {
        return (bool)($this->source->required ?? false);
    }

    /** @inheritdoc */
    public function getValidator(): ?string {
        return isset($this->source->validator) ? (string)$this->source->validator : null;
    }

    /** @inheritdoc */
    public function getDefault() {
        return isset($this->source->default) ? $this->source->default : null;
    }

    /** @inheritdoc */
    public function getFilter(string $name): ?string {
        return isset($this->source->filters[$name]) ? (string)$this->source->filters[$name] : null;
    }

    /** @inheritdoc */
    public function hasSchema(): bool {
        return property_exists($this->source, 'schema') !== false && $this->source->schema !== null;
    }

    /** @inheritdoc */
    public function getSchema(): string {
        if ($this->hasSchema() === false) {
            throw new SchemaException(sprintf('Property [%s] does not have a reference to schema.', $this->getName()));
        }
        return (string)$this->source->schema;
    }
}
