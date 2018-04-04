<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\SimpleObject;

	class Schema extends SimpleObject implements ISchema {
		/** @var string */
		protected $name;
		/** @var string|null */
		protected $alias;
		/** @var IAttribute[] */
		protected $attributes = [];
		/** @var IAttribute */
		protected $primary;
		/** @var IAttribute[] */
		protected $uniques = null;

		public function __construct(string $name, array $attributes, string $alias = null) {
			$this->name = $name;
			$this->attributes = $attributes;
			$this->alias = $alias;
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name;
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
		public function hasPrimary(): bool {
			try {
				$this->getPrimary();
				return true;
			} catch (SchemaException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function getPrimary(): IAttribute {
			if ($this->primary) {
				return $this->primary;
			}
			foreach ($this->attributes as $attribute) {
				if ($attribute->isPrimary()) {
					return $this->primary = $attribute;
				}
			}
			throw new SchemaException(sprintf('Schema [%s] has no primary attribute.', $this->getName()));
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
