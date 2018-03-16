<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Api\Node\INode;
	use Edde\Object;
	use Edde\Schema\IProperty;
	use Edde\Schema\IPropertyBuilder;

	class PropertyBuilder extends Object implements IPropertyBuilder {
		/** @var INode */
		protected $root;
		/** @var INode */
		protected $node;
		/** @var IProperty */
		protected $property;

		public function __construct(INode $root, INode $node) {
			$this->root = $root;
			$this->node = $node;
		}

		/** @inheritdoc */
		public function type(string $type): IPropertyBuilder {
			$this->node->setAttribute('type', $type);
			return $this;
		}

		/** @inheritdoc */
		public function unique(bool $unique = true): IPropertyBuilder {
			$this->node->setAttribute('unique', $unique);
			$this->required($unique);
			return $this;
		}

		/** @inheritdoc */
		public function primary(bool $primary = true): IPropertyBuilder {
			$this->node->setAttribute('primary', $primary);
			$this->required($primary);
			$this->unique($primary);
			return $this;
		}

		/** @inheritdoc */
		public function required(bool $required = true): IPropertyBuilder {
			$this->node->setAttribute('required', $required);
			return $this;
		}

		/** @inheritdoc */
		public function generator(string $string): IPropertyBuilder {
			$this->node->setAttribute('generator', $string);
			return $this;
		}

		/** @inheritdoc */
		public function filter(string $name): \Edde\Schema\IPropertyBuilder {
			$this->node->setAttribute('filter', $name);
			return $this;
		}

		/** @inheritdoc */
		public function sanitizer(string $name): IPropertyBuilder {
			$this->node->setAttribute('sanitizer', $name);
			return $this;
		}

		/** @inheritdoc */
		public function validator(string $name): IPropertyBuilder {
			$this->node->setAttribute('validator', $name);
			return $this;
		}

		/** @inheritdoc */
		public function link(): \Edde\Schema\IPropertyBuilder {
			$this->node->setAttribute('link', true);
			return $this;
		}

		/** @inheritdoc */
		public function default($default): IPropertyBuilder {
			$this->node->setAttribute('default', $default);
			return $this;
		}

		/** @inheritdoc */
		public function getProperty(): IProperty {
			return $this->property ?: $this->property = new Property($this->root, $this->node);
		}
	}
