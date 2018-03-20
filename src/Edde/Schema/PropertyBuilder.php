<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Object;

	class PropertyBuilder extends Object implements IPropertyBuilder {
		protected $source;
		/** @var IProperty */
		protected $property;

		public function __construct(string $name) {
			$this->source = (object)['name' => $name];
		}

		/** @inheritdoc */
		public function type(string $type): IPropertyBuilder {
			$this->source->type = $type;
			return $this;
		}

		/** @inheritdoc */
		public function unique(bool $unique = true): IPropertyBuilder {
			$this->source->unique = $unique;
			$this->required($unique);
			return $this;
		}

		/** @inheritdoc */
		public function primary(bool $primary = true): IPropertyBuilder {
			$this->source->primary = $primary;
			$this->required($primary);
			$this->unique($primary);
			return $this;
		}

		/** @inheritdoc */
		public function required(bool $required = true): IPropertyBuilder {
			$this->source->required = $required;
			return $this;
		}

		/** @inheritdoc */
		public function generator(string $generator): IPropertyBuilder {
			$this->source->generator = $generator;
			return $this;
		}

		/** @inheritdoc */
		public function filter(string $filter): IPropertyBuilder {
			$this->source->filter = $filter;
			return $this;
		}

		/** @inheritdoc */
		public function sanitizer(string $sanitizer): IPropertyBuilder {
			$this->source->sanitizer = $sanitizer;
			return $this;
		}

		/** @inheritdoc */
		public function validator(string $validator): IPropertyBuilder {
			$this->source->validator = $validator;
			return $this;
		}

		/** @inheritdoc */
		public function link(): IPropertyBuilder {
			$this->source->link = true;
			return $this;
		}

		/** @inheritdoc */
		public function default($default): IPropertyBuilder {
			$this->source->default = $default;
			return $this;
		}

		/** @inheritdoc */
		public function getProperty(): IProperty {
			return $this->property ?: $this->property = new Property($this->source);
		}
	}
