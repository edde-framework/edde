<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Crate\IProperty;
		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Crate\Crate;

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
			public function filter(array $source): IEntity {
				$this->push($this->schemaManager->filter($this->schema, $source));
				return $this;
			}

			/** @inheritdoc */
			public function linkTo(IEntity $entity): IEntity {
				$this->entityQueue->link($this, $entity, $this->schema->getLink($entity->getSchema()->getName()));
				return $this;
			}

			/** @inheritdoc */
			public function unlink(string $schema): IEntity {
				$this->entityQueue->unlink($this, $this->schema->getLink($schema));
				return $this;
			}

			/** @inheritdoc */
			public function attach(IEntity $entity): IEntity {
				$relation = $this->schema->getRelation($entity->getSchema()->getName());
				$use = $this->entityManager->create($relation->getSchema()->getName());
				$this->entityQueue->attach($this, $entity, $use, $relation);
				return $use;
			}

			/** @inheritdoc */
			public function detach(IEntity $entity): IEntity {
				$relation = $this->schema->getRelation($entity->getSchema()->getName());
				$this->entityQueue->detach($this, $entity, $relation);
				return $this;
			}

			/** @inheritdoc */
			public function join(string $schema, string $alias): ICollection {
				$collection = $this->entityManager->collection($this->schema->getName());
				$collection->join($schema, $alias, $this->toArray());
				$collection->return($alias);
				return $collection;
			}

			/** @inheritdoc */
			public function delete(): IEntity {
				$this->entityQueue->delete($this);
				return $this;
			}

			/** @inheritdoc */
			public function save(): IEntity {
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
			public function sanitize(): array {
				return $this->schemaManager->sanitize($this->schema, $this->toArray());
			}

			/** @inheritdoc */
			public function toArray(): array {
				$array = [];
				foreach ($this->schema->getPropertyList() as $k => $property) {
					$array[$k] = $this->get($k);
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
