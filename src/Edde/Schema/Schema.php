<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\SimpleObject;

	class Schema extends SimpleObject implements ISchema {
		/** @var string */
		protected $name;
		/** @var IAttribute */
		protected $primary;
		/** @var string|null */
		protected $alias;
		/** @var IAttribute[] */
		protected $attributes = [];
		/** @var array */
		protected $meta = [];
		/** @var IAttribute[] */
		protected $uniques = null;

		public function __construct(string $name, IAttribute $primary, array $attributes, array $meta = [], string $alias = null) {
			$this->name = $name;
			$this->primary = $primary;
			$this->attributes = $attributes;
			$this->meta = $meta;
			$this->alias = $alias;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
		}

		/** @inheritdoc */
		public function getPrimary(): IAttribute {
			return $this->primary;
		}

		/** @inheritdoc */
		public function hasAlias(): bool {
			return $this->alias !== null;
		}

		/** @inheritdoc */
		public function getAlias(): string {
			return $this->alias;
		}

		/** @inheritdoc */
		public function getRealName(): string {
			return $this->alias ?: $this->name;
		}

		/** @inheritdoc */
		public function getMeta(string $name, $default = null) {
			return $this->meta[$name] ?? $default;
		}

		/** @inheritdoc */
		public function getAttribute(string $name): IAttribute {
			if (isset($this->attributes[$name]) === false) {
				throw new SchemaException(sprintf('Requested unknown attribute [%s::%s].', $this->getName(), $name));
			}
			return $this->attributes[$name];
		}

		/** @inheritdoc */
		public function getAttributes(): array {
			return $this->attributes;
		}

		/** @inheritdoc */
		public function getUniques(): array {
			if ($this->uniques) {
				return $this->uniques;
			}
			$this->uniques = [];
			foreach ($this->attributes as $name => $attribute) {
				if ($attribute->isUnique() && $attribute->isPrimary() === false) {
					$this->uniques[$name] = $attribute;
				}
			}
			return $this->uniques;
		}
	}
