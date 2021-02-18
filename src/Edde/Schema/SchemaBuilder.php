<?php
declare(strict_types=1);

namespace Edde\Schema;

use Edde\Edde;
use stdClass;

class SchemaBuilder extends Edde implements ISchemaBuilder {
    /** @var stdClass */
    protected $source;
    /** @var IAttributeBuilder[] */
    protected $propertyBuilders = [];
    /** @var ISchema */
    protected $schema;

    public function __construct(string $name) {
        $this->source = (object)['name' => $name];
    }

    /** @inheritdoc */
    public function alias(string $alias): ISchemaBuilder {
        $this->source->alias = $alias;
        return $this;
    }

    /** @inheritdoc */
    public function meta(array $meta): ISchemaBuilder {
        $this->source->meta = $meta;
        return $this;
    }

    /** @inheritdoc */
    public function property(string $name): IAttributeBuilder {
        return $this->propertyBuilders[$name] = new AttributeBuilder($name);
    }

    /** @inheritdoc */
    public function relation(string $source, string $target): ISchemaBuilder {
        $this->source->relation = (object)[
            'source' => $source,
            'target' => $target,
        ];
        return $this;
    }

    /** @inheritdoc */
    public function create(): ISchema {
        if ($this->schema) {
            return $this->schema;
        }
        $attributes = [];
        $primary = null;
        foreach ($this->propertyBuilders as $name => $propertyBuilder) {
            $attributes[$name] = $attribute = $propertyBuilder->getAttribute();
            if ($attribute->isPrimary()) {
                $primary = $attribute;
            }
        }
        return $this->schema = new Schema(
            $this->source,
            $primary,
            $attributes
        );
    }
}
