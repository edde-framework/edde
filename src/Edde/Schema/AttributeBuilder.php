<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Obj3ct;
	use stdClass;

	class AttributeBuilder extends Obj3ct implements IAttributeBuilder {
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
		public function generator(string $generator): IAttributeBuilder {
			$this->source->generator = $generator;
			return $this;
		}

		/** @inheritdoc */
		public function filter(string $filter): IAttributeBuilder {
			$this->source->filter = $filter;
			return $this;
		}

		/** @inheritdoc */
		public function sanitizer(string $sanitizer): IAttributeBuilder {
			$this->source->sanitizer = $sanitizer;
			return $this;
		}

		/** @inheritdoc */
		public function validator(string $validator): IAttributeBuilder {
			$this->source->validator = $validator;
			return $this;
		}

		/** @inheritdoc */
		public function link(): IAttributeBuilder {
			$this->source->link = true;
			return $this;
		}

		/** @inheritdoc */
		public function default($default): IAttributeBuilder {
			$this->source->default = $default;
			return $this;
		}

		/** @inheritdoc */
		public function getAttribute(): IAttribute {
			return $this->attribute ?: $this->attribute = new Attribute($this->source);
		}
	}
