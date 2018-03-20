<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Collection\HashMap;
	use Edde\Collection\IHashMap;
	use Edde\Node\INode;

	class PropertyBuilder extends HashMap implements IPropertyBuilder {
		/** @var INode */
		protected $root;
		/** @var IProperty */
		protected $property;

		public function __construct(IHashMap $root, string $name) {
			parent::__construct((object)['name' => $name]);
			$this->root = $root;
		}

		/** @inheritdoc */
		public function type(string $type): IPropertyBuilder {
			$this->set('type', $type);
			return $this;
		}

		/** @inheritdoc */
		public function unique(bool $unique = true): IPropertyBuilder {
			$this->set('unique', $unique);
			$this->required($unique);
			return $this;
		}

		/** @inheritdoc */
		public function primary(bool $primary = true): IPropertyBuilder {
			$this->set('primary', $primary);
			$this->required($primary);
			$this->unique($primary);
			return $this;
		}

		/** @inheritdoc */
		public function required(bool $required = true): IPropertyBuilder {
			$this->set('required', $required);
			return $this;
		}

		/** @inheritdoc */
		public function generator(string $string): IPropertyBuilder {
			$this->set('generator', $string);
			return $this;
		}

		/** @inheritdoc */
		public function filter(string $name): IPropertyBuilder {
			$this->set('filter', $name);
			return $this;
		}

		/** @inheritdoc */
		public function sanitizer(string $name): IPropertyBuilder {
			$this->set('sanitizer', $name);
			return $this;
		}

		/** @inheritdoc */
		public function validator(string $name): IPropertyBuilder {
			$this->set('validator', $name);
			return $this;
		}

		/** @inheritdoc */
		public function link(): IPropertyBuilder {
			$this->set('link', true);
			return $this;
		}

		/** @inheritdoc */
		public function default($default): IPropertyBuilder {
			$this->set('default', $default);
			return $this;
		}

		/** @inheritdoc */
		public function getProperty(): IProperty {
			return $this->property ?: $this->property = new Property($this->root, $this);
		}
	}
