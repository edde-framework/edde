<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Crate\IProperty;
	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\IEntityQueue;
	use Edde\Api\Entity\Query\IDetachQuery;
	use Edde\Api\Entity\Query\IDisconnectQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Crate\Crate;
	use Edde\Exception\Driver\DriverException;
	use Edde\Exception\Entity\RecordException;
	use Edde\Exception\Filter\FilterException;
	use Edde\Exception\Filter\UnknownFilterException;
	use Edde\Exception\Sanitizer\SanitizerException;
	use Edde\Exception\Sanitizer\UnknownSanitizerException;
	use Edde\Exception\Schema\LinkException;
	use Edde\Exception\Schema\NoPrimaryPropertyException;
	use Edde\Exception\Schema\RelationException;
	use Edde\Exception\Schema\UnknownPropertyException;
	use Edde\Exception\Schema\UnknownSchemaException;
	use Edde\Exception\Validator\ValidationException;
	use Edde\Inject\Entity\EntityManager;
	use Edde\Inject\Schema\SchemaManager;

	class Entity extends Crate implements IEntity {
		use EntityManager;
		use SchemaManager;
		/** @var ISchema */
		protected $schema;
		/** @var IEntityQueue */
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

		/**
		 * @inheritdoc
		 *
		 * @throws NoPrimaryPropertyException
		 */
		public function getPrimary(): IProperty {
			return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
		}

		/**
		 * @inheritdoc
		 *
		 * @throws NoPrimaryPropertyException
		 */
		public function getHash(): string {
			return $this->getPrimary()->get();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws LinkException
		 */
		public function linkTo(IEntity $entity): IEntity {
			$this->entityQueue->link($this, $entity, $this->schema->getLink($entity->getSchema()->getName()));
			return $this;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws LinkException
		 * @throws \Edde\Exception\Schema\SchemaException
		 * @throws \Edde\Exception\Schema\UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws \Edde\Exception\Storage\EntityNotFoundException
		 * @throws \Edde\Exception\Storage\UnknownTableException
		 * @throws RecordException
		 */
		public function link(string $schema): IEntity {
			$link = $this->schema->getLink($schema);
			$collection = $this->entityManager->collection('c', $this->schema->getName());
			$collection->getQuery()->link('l', $schema);
			$collection->schema('l', $this->schemaManager->load($schema));
			$collection->where('l.' . $link->getTo()->getPropertyName(), '=', $this->get($link->getFrom()->getPropertyName()));
			return $collection->getEntity('l');
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Schema\LinkException
		 */
		public function unlink(string $schema): IEntity {
			$this->entityQueue->unlink($this, $this->schema->getLink($schema));
			return $this;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Schema\SchemaException
		 * @throws UnknownSchemaException
		 * @throws RelationException
		 */
		public function attach(IEntity $entity, string $relation = null): IEntity {
			$relation = $this->schema->getRelation($entity->getSchema()->getName(), $relation);
			$use = $this->entityManager->create($relation->getSchema()->getName());
			$this->entityQueue->attach($this, $entity, $use, $relation);
			return $use;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws RelationException
		 */
		public function detach(IEntity $entity, string $relation = null): IDetachQuery {
			return $this->entityQueue->detach($this, $entity, $this->schema->getRelation($entity->getSchema()->getName(), $relation));
		}

		/**
		 * @inheritdoc
		 *
		 * @throws RelationException
		 */
		public function disconnect(string $schema): IDisconnectQuery {
			return $this->entityQueue->disconnect($this, $this->schema->getRelation($schema));
		}

		/** @inheritdoc
		 */
		public function join(string $alias, string $schema, string $relation = null): ICollection {
			$collection = $this->entityManager->collection('e', $this->schema->getName());
			$collection->join('e', $schema, $alias, $this->toArray(), $relation);
			return $collection;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Schema\SchemaException
		 * @throws UnknownSchemaException
		 */
		public function reverseJoin(string $alias, string $schema, string $relation = null): ICollection {
			$collection = $this->entityManager->collection($alias, $schema);
			$collection->reverseJoin($alias, $this->schema->getName(), 'e', $this->toArray(), $relation);
			return $collection;
		}

		/** @inheritdoc */
		public function delete(): IEntity {
			$this->entityQueue->delete($this);
			return $this;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ValidationException
		 * @throws \Edde\Exception\Storage\DuplicateEntryException
		 * @throws \Edde\Exception\Storage\StorageException
		 * @throws \Edde\Exception\Validator\BatchValidationException
		 * @throws DriverException
		 */
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

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Schema\UnknownPropertyException
		 * @throws FilterException
		 * @throws UnknownFilterException
		 */
		public function filter(array $source): IEntity {
			$this->push($this->schemaManager->filter($this->schema, $source));
			return $this;
		}

		/**
		 * @inheritdoc
		 *
		 * @throws UnknownPropertyException
		 * @throws SanitizerException
		 * @throws UnknownSanitizerException
		 */
		public function sanitize(): array {
			return $this->schemaManager->sanitize($this->schema, $this->toArray());
		}

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Exception\Validator\UnknownValidatorException
		 */
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

		/**
		 * @inheritdoc
		 *
		 * @throws ValidationException
		 * @throws \Edde\Exception\Validator\UnknownValidatorException
		 */
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
