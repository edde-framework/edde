<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\SimpleObject;
	use stdClass;
	use function property_exists;

	class Schema extends SimpleObject implements ISchema {
		/** @var stdClass */
		protected $source;
		/** @var IAttribute */
		protected $primary;
		/** @var IAttribute[] */
		protected $attributes = [];
		/** @var IAttribute[] */
		protected $uniques = null;

		public function __construct(stdClass $source, IAttribute $primary, array $attributes) {
			$this->source = $source;
			$this->primary = $primary;
			$this->attributes = $attributes;
		}

		/** @inheritdoc */
		public function getName(): string {
			return (string)$this->source->name;
		}

		/** @inheritdoc */
		public function getPrimary(): IAttribute {
			return $this->primary;
		}

		/** @inheritdoc */
		public function hasAlias(): bool {
			return property_exists($this->source, 'alias') && $this->source->alias !== null;
		}

		/** @inheritdoc */
		public function getAlias(): ?string {
			return property_exists($this->source, 'alias') ? (string)$this->source->alias : null;
		}

		/** @inheritdoc */
		public function getRealName(): string {
			return $this->getAlias() ?: $this->getName();
		}

		/** @inheritdoc */
		public function getMeta(string $name, $default = null) {
			return $this->source->meta ?? $default;
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

		/** @inheritdoc */
		public function isRelation(): bool {
			return property_exists($this->source, 'relation');
		}
	}
