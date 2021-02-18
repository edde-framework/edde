<?php
declare(strict_types=1);

namespace Edde\Schema;

use Edde\Edde;
use stdClass;

class AttributeBuilder extends Edde implements IAttributeBuilder {
    /** @var stdClass */
    protected $source;
    /** @var IAttribute */
    protected $attribute;

    public function __construct(string $name) {
        $this->source = (object)['name' => $name];
    }

    /** @inheritdoc */
    public function type(string $type): IAttributeBuilder {
        $this->source->type = $type;
        return $this;
    }

    /** @inheritdoc */
    public function unique(bool $unique = true): IAttributeBuilder {
        $this->source->unique = $unique;
        $this->required($unique);
        return $this;
    }

    /** @inheritdoc */
    public function primary(bool $primary = true): IAttributeBuilder {
        $this->source->primary = $primary;
        $this->required($primary);
        $this->unique($primary);
        return $this;
    }

    /** @inheritdoc */
    public function required(bool $required = true): IAttributeBuilder {
        $this->source->required = $required;
        return $this;
    }

    /** @inheritdoc */
    public function filter(string $type, string $filter): IAttributeBuilder {
        $this->source->filters[$type] = $filter;
        return $this;
    }

    /** @inheritdoc */
    public function validator(string $validator): IAttributeBuilder {
        $this->source->validator = $validator;
        return $this;
    }

    /** @inheritdoc */
    public function default($default): IAttributeBuilder {
        $this->source->default = $default;
        return $this;
    }

    /** @inheritdoc */
    public function schema(string $schema): IAttributeBuilder {
        $this->source->schema = $schema;
        return $this;
    }

    /** @inheritdoc */
    public function getAttribute(): IAttribute {
        return $this->attribute ?: $this->attribute = new Attribute($this->source);
    }
}
