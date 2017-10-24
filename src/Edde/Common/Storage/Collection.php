<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\ISchemaManager;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		class Collection extends Object implements ICollection {
			/**
			 * @var ISchemaManager
			 */
			protected $schemaManager;
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
			 * query being executed on the storage
			 *
			 * @var IQuery
			 */
			protected $query;
			/**
			 * @var string
			 */
			protected $schema;

			public function __construct(ISchemaManager $schemaManager, IEntityManager $entityManager, IStorage $storage, IQuery $query, string $schema) {
				$this->schemaManager = $schemaManager;
				$this->entityManager = $entityManager;
				$this->storage = $storage;
				$this->query = $query;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->storage->execute($this->query) as $source) {
					yield $this->entityManager->factory($this->schema, $this->schemaManager->filter($this->schema, $source));
				}
			}
		}
