<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;

		abstract class AbstractStorage extends Object implements IStorage {
			use SchemaManager;

			/**
			 * @inheritdoc
			 */
			public function createEntity(string $schema, array $source = []): IEntity {
				$entity = new Entity($this->schemaManager->getSchema($schema));
				$entity->update($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function load(IQuery $query): IEntity {
				foreach ($this->collection($query) as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $query->getDescription()));
			}
		}
