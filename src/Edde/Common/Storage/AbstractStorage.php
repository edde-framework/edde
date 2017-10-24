<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\CreateSchemaQuery;

		abstract class AbstractStorage extends Object implements IStorage {
			use EntityManager;
			use SchemaManager;

			/**
			 * @inheritdoc
			 */
			public function createSchema(string $schema): IStorage {
				/**
				 * because storage is using IQL in general, it's possible to safely use queries here in abstract
				 * implementation
				 */
				$this->execute(new CreateSchemaQuery($this->schemaManager->getSchema($schema)));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema): ICollection {
				$collection = new Collection($this->entityManager, $this, $schema);
				/**
				 * by default select all from the source schema
				 */
				$collection->table($schema)->all();
				return $collection;
			}
		}
