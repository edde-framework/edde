<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Query\SelectQuery;

		class Collection extends SelectQuery implements ICollection {
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
			 * @var string
			 */
			protected $schema;

			public function __construct(IEntityManager $entityManager, IStorage $storage, string $schema) {
				$this->entityManager = $entityManager;
				$this->storage = $storage;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntity(): IEntity {
				foreach ($this as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $this->getDescription()));
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->storage->execute($this) as $source) {
					yield $this->entityManager->factory($this->schema, $source);
				}
			}
		}
