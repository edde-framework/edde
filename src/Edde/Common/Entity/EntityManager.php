<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityManager;
		use Edde\Api\Entity\Inject\Transaction;
		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\SelectQuery;

		class EntityManager extends Object implements IEntityManager {
			use SchemaManager;
			use Transaction;
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
				$this->transaction->entity($entity = $this->createEntity($schema = $this->schemaManager->load($schema))->put($this->schemaManager->generate($schema, $source)));
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function load(ISchema $schema, array $source): IEntity {
				$entity = $this->createEntity($schema);
				$entity->filter($source);
				$entity->exists(true);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema): ICollection {
				if (isset($this->collectionList[$name = $schema]) === false) {
					$this->collectionList[$name] = $this->container->inject(new Collection($this->storage->stream(new SelectQuery($schema = $this->schemaManager->load($schema), 'c')), $schema));
				}
				return clone $this->collectionList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function transaction(): ITransaction {
				return clone $this->transaction;
			}
		}
