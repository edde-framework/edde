<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Common\Object\Object;

		class EntityManager extends Object implements IEntityManager {
			use SchemaManager;
			use Container;
			/**
			 * @var IEntity[]
			 */
			protected $entityList = [];

			/**
			 * @inheritdoc
			 */
			public function createEntity(ISchema $schema): IEntity {
				if (isset($this->entityList[$name = $schema->getName()]) === false) {
					$this->entityList[$name] = $this->container->inject(new Entity($schema));
				}
				return clone $this->entityList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function create(string $schema, array $source = []): IEntity {
				$entity = $this->createEntity($this->schemaManager->load($schema));
				$entity->put($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function factory(string $schema, array $source): IEntity {
				$entity = $this->createEntity($this->schemaManager->load($schema));
				$entity->push($this->schemaManager->filter($schema, $source));
				return $entity;
			}
		}
