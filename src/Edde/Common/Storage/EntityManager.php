<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityManagerException;
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
				$schema = $this->schemaManager->getSchema($relation);
				$relation = $this->createEntity(($schema)->getName());
				$this->link($relation, $entity, $schema);
				$this->link($relation, $to, $schema);
				return $relation;
			}

			/**
			 * @param IEntity $relation
			 * @param IEntity $entity
			 * @param ISchema $schema
			 *
			 * @throws EntityManagerException
			 */
			protected function link(IEntity $relation, IEntity $entity, ISchema $schema) {
				if (count($link = $schema->getRelationList($entity->getSchema()->getName())) > 1) {
					throw new EntityManagerException(sprintf('Invalid schema //generate better exception text'));
				}
				list($link) = $link;
				$relation->set(($link = $link->getLink())->getProperty(), $entity->get($link->getTarget()));
			}
		}
