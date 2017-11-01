<?php
	namespace Edde\Common\Entity;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityManager;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
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
				$entity = $this->createEntity($schema = $this->schemaManager->load($schema));
				$entity->put($this->schemaManager->generate($schema, $source));
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function factory(string $schema, array $source): IEntity {
				$entity = $this->createEntity($schema = $this->schemaManager->load($schema));
				$entity->push($this->schemaManager->filter($schema, $source));
				return $entity;
			}
		}
