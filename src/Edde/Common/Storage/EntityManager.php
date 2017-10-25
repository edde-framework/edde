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
			public function createEntity(string $schema): IEntity {
				return new Entity($this->schemaManager->getSchema($schema));
			}

			/**
			 * @inheritdoc
			 */
			public function create(string $schema, array $source = []): IEntity {
				$entity = $this->createEntity($schema);
				$entity->update($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function factory(string $schema, array $source): IEntity {
				$entity = $this->createEntity($schema);
				$entity->push($this->schemaManager->filter($schema, $source));
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity, IEntity $to, string $relation): IEntity {
				$relation = $this->createEntity(($schema = $this->schemaManager->getSchema($relation))->getName());
				$relation->set($schema->getLink($entity->getSchema()->getName())->getProperty(), '');
//				$schema->getLink($to->getSchema()->getName());
//				$entity->set()
				return $relation;
			}
		}
