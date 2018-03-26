<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\Crate;
	use Edde\Crate\IProperty;
	use Edde\Schema\ISchema;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Validator\ValidationException;

	class Entity extends Crate implements IEntity {
		use EntityManager;
		use SchemaManager;
		/** @var ISchema */
		protected $schema;
		/** @var EntityQueue */
		protected $entityQueue;
		/** @var IProperty */
		protected $primary = null;

		public function __construct(ISchema $schema) {
			$this->schema = $schema;
			$this->entityQueue = new EntityQueue();
		}

		/** @inheritdoc */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getPrimary(): IProperty {
			return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
		}

		/** @inheritdoc */
		public function save(): IEntity {
			$this->validate();
			foreach ($this->entityQueue as $entity) {
				if ($entity === $this) {
					continue;
				}
				$entity->save();
			}
			$this->entityQueue->queue($this);
			$this->entityManager->execute($this->entityQueue);
			return $this;
		}

		/** @inheritdoc */
		public function filter(array $source): IEntity {
			$this->push($this->schemaManager->filter($this->schema, $source));
			return $this;
		}

		/** @inheritdoc */
		public function sanitize(): array {
			return $this->schemaManager->sanitize($this->schema, $this->toArray());
		}

		/** @inheritdoc */
		public function isValid(): bool {
			try {
				/**
				 * exception is quite expensive, but the validation logic is simply in
				 * one method
				 */
				$this->validate();
				return true;
			} catch (ValidationException $exception) {
				return false;
			}
		}

		/** @inheritdoc */
		public function validate(): IEntity {
			$this->schemaManager->validate($this->schema, $this->toArray());
			return $this;
		}

		/** @inheritdoc */
		public function toArray(): array {
			$array = [];
			foreach ($this->schema->getProperties() as $k => $property) {
				$array[$k] = $this->get($k, $property->getDefault());
			}
			return $array;
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->primary = null;
			$this->entityQueue = clone $this->entityQueue;
		}
	}
