<?php
	declare(strict_types=1);
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
				return $this->createEntity($schema = $this->schemaManager->load($schema))->put($this->schemaManager->generate($schema, $source));
			}

			/**
			 * @inheritdoc
			 */
			public function load(string $schema, array $source): IEntity {
				return $this->createEntity($this->schemaManager->load($schema))->load($source);
			}

			/**
			 * @inheritdoc
			 */
			public function collection(string $schema): ICollection {
				if (isset($this->collectionList[$name = $schema]) === false) {
					$query = new SelectQuery();
					$query->schema($this->schemaManager->load($schema), 'c')->select();
					$this->collectionList[$name] = $this->container->inject(new Collection($this->storage->stream($query), $schema));
				}
				return clone $this->collectionList[$name];
			}
		}
