<?php
	namespace Edde\Common\Entity;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityManager;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\SelectQuery;

		class EntityManager extends Object implements IEntityManager {
			use SchemaManager;
			use Container;
			use Storage;
			/**
			 * @var IEntity[]
			 */
			protected $entityList = [];
			/**
			 * @var ICollection[]
			 */
			protected $collectionList = [];

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

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema): ICollection {
				if (isset($this->collectionList[$schema]) === false) {
					$query = new SelectQuery();
					$query->table($schema)->all();
					$this->collectionList[$schema] = $this->container->inject(new Collection($this->storage->stream($query), $this->schemaManager->load($schema)));
				}
				return clone $this->collectionList[$schema];
			}
		}
