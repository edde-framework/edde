<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		class Collection extends Object implements ICollection {
			/**
			 * @var IEntityManager
			 */
			protected $entityManager;
			/**
			 * source for this collection
			 *
			 * @var IStorage
			 */
			protected $storage;
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var ISelectQuery
			 */
			protected $query;

			public function __construct(IEntityManager $entityManager, IStorage $storage, ISchema $schema, ISelectQuery $query) {
				$this->entityManager = $entityManager;
				$this->storage = $storage;
				$this->schema = $schema;
				$this->query = $query;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): ISelectQuery {
				return $this->query;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntity(): IEntity {
				foreach ($this as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $this->query->getDescription()));
			}

			/**
			 * @inheritdoc
			 */
			public function load($value): IEntity {
				foreach ($this->schema->getPrimaryList() as $property) {
					$this->query->where()->or()->eq($property->getName())->to($value);
				}
				foreach ($this->schema->getUniqueList() as $property) {
					$this->query->where()->or()->eq($property->getName())->to($value);
				}
				return $this->getEntity();
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->storage->execute($this->query) as $source) {
					yield $this->entityManager->factory($this->schema->getName(), $source);
				}
			}
		}
