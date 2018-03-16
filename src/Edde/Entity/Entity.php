<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\Crate;
	use Edde\Crate\IProperty;
	use Edde\Exception\Validator\ValidationException;
	use Edde\Inject\Entity\EntityManager;
	use Edde\Inject\Schema\SchemaManager;
	use Edde\Query\IDetachQuery;
	use Edde\Query\IDisconnectQuery;
	use Edde\Schema\ISchema;

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

		/** @inheritdoc */
		public function getPrimary(): IProperty {
			return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
		}

		/** @inheritdoc */
		public function getHash(): string {
			return $this->getPrimary()->get();
		}

		/** @inheritdoc */
		public function linkTo(IEntity $entity): IEntity {
			$this->entityQueue->link($this, $entity, $this->schema->getLink($entity->getSchema()->getName()));
			return $this;
		}

		/** @inheritdoc */
		public function link(string $schema): IEntity {
			$link = $this->schema->getLink($schema);
			$collection = $this->entityManager->collection('c', $this->schema->getName());
			$collection->getQuery()->link('l', $schema);
			$collection->schema('l', $this->schemaManager->load($schema));
			$collection->where('l.' . $link->getTo()->getPropertyName(), '=', $this->get($link->getFrom()->getPropertyName()));
			return $collection->getEntity('l');
		}

		/** @inheritdoc */
		public function unlink(string $schema): IEntity {
			$this->entityQueue->unlink($this, $this->schema->getLink($schema));
			return $this;
		}

		/** @inheritdoc */
		public function attach(IEntity $entity, string $relation = null): IEntity {
			$relation = $this->schema->getRelation($entity->getSchema()->getName(), $relation);
			$use = $this->entityManager->create($relation->getSchema()->getName());
			$this->entityQueue->attach($this, $entity, $use, $relation);
			return $use;
		}

		/** @inheritdoc */
		public function detach(IEntity $entity, string $relation = null): IDetachQuery {
			return $this->entityQueue->detach($this, $entity, $this->schema->getRelation($entity->getSchema()->getName(), $relation));
		}

		/** @inheritdoc */
		public function disconnect(string $schema): IDisconnectQuery {
			return $this->entityQueue->disconnect($this, $this->schema->getRelation($schema));
		}

		/** @inheritdoc
		 */
		public function join(string $alias, string $schema, string $relation = null): \Edde\Entity\ICollection {
			$collection = $this->entityManager->collection('e', $this->schema->getName());
			$collection->join('e', $schema, $alias, $this->toArray(), $relation);
			return $collection;
		}

		/** @inheritdoc */
		public function reverseJoin(string $alias, string $schema, string $relation = null): \Edde\Entity\ICollection {
			$collection = $this->entityManager->collection($alias, $schema);
			$collection->reverseJoin($alias, $this->schema->getName(), 'e', $this->toArray(), $relation);
			return $collection;
		}

		/** @inheritdoc */
		public function delete(): IEntity {
			$this->entityQueue->delete($this);
			return $this;
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
