<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Common\Object\Object;

		class EntityManager extends Object implements IEntityManager {
			use SchemaManager;

			/**
			 * @inheritdoc
			 */
			public function create(string $schema, array $source = []): IEntity {
				$entity = new Entity($this->schemaManager->getSchema($schema));
				$entity->update($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(IEntity $entity): bool {
				return $entity->isDirty();
			}

			/**
			 * @inheritdoc
			 */
			public function getDirtyProperties(IEntity $entity): array {
				return $entity->getDirtyProperties();
			}
		}
