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
			return property_exists($this->source, 'relation') && $this->source->relation !== null;
		}

		/** @inheritdoc */
		public function getSource(): IAttribute {
			if ($this->isRelation() === false) {
				throw new SchemaException(sprintf('Schema [%s] is not relation; relation source is not available!', $this->getName()));
			}
			return $this->getAttribute($this->source->relation->source);
		}

		/** @inheritdoc */
		public function getTarget(): IAttribute {
			if ($this->isRelation() === false) {
				throw new SchemaException(sprintf('Schema [%s] is not relation; relation target is not available!', $this->getName()));
			}
			return $this->getAttribute($this->source->relation->target);
		}

		/** @inheritdoc */
		public function checkRelation(ISchema $source, ISchema $target): void {
			$sourceAttribute = $this->getSource();
			$targetAttribute = $this->getTarget();
			if ($this->isRelation() === false) {
				throw new SchemaException(vsprintf('Invalid relation (%s)-[!%s]->(%s): Relation schema [%s] is not a relation.', [
					$source->getName(),
					$this->getName(),
					$target->getName(),
					$this->getName(),
				]));
			} else if (($expectedSchemaName = $sourceAttribute->getSchema()) !== $source->getName()) {
				throw new SchemaException(vsprintf('Invalid relation (!%s)-[%s]->(%s): Source schema [%s] differs from expected relation [%s]; did you swap $source and $target schema?.', [
					$source->getName(),
					$this->getName(),
					$target->getName(),
					$source->getName(),
					$expectedSchemaName,
				]));
			} else if (($expectedSchemaName = $targetAttribute->getSchema()) !== $target->getName()) {
				throw new SchemaException(vsprintf('Invalid relation (%s)-[%s]->(!%s): Target schema [%s] differs from expected relation [%s]; did you swap $source and $target schema?.', [
					$source->getName(),
					$this->getName(),
					$target->getName(),
					$target->getName(),
					$expectedSchemaName,
				]));
			}
		}
	}
