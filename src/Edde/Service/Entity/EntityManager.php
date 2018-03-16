<?php
	declare(strict_types=1);
	namespace Edde\Service\Entity;

	use Edde\Common\Storage\Query\SelectQuery;
	use Edde\Entity\Collection;
	use Edde\Entity\Entity;
	use Edde\Entity\IEntity;
	use Edde\Entity\IEntityManager;
	use Edde\Entity\IEntityQueue;
	use Edde\Inject\Container\Container;
	use Edde\Inject\Schema\SchemaManager;
	use Edde\Inject\Storage\Storage;
	use Edde\Object;
	use Edde\Query\QueryQueue;
	use Edde\Schema\ISchema;

	class EntityManager extends Object implements IEntityManager {
		use SchemaManager;
		use Storage;
		use Container;
		/** @var \Edde\Entity\IEntity[] */
		protected $entities = [];

		/** @inheritdoc */
		public function createEntity(ISchema $schema): IEntity {
			if (isset($this->entities[$name = $schema->getName()]) === false) {
				$this->entities[$name] = $this->container->inject(new Entity($schema));
			}
			return clone $this->entities[$name];
		}

		/** @inheritdoc */
		public function create(string $schema, array $source = []): \Edde\Entity\IEntity {
			return $this->createEntity($schema = $this->schemaManager->load($schema))->put($this->schemaManager->generate($schema, $source));
		}

		/** @inheritdoc */
		public function load(ISchema $schema, array $source): \Edde\Entity\IEntity {
			$entity = $this->createEntity($schema);
			$entity->filter($source);
			return $entity;
		}

		/** @inheritdoc */
		public function collection(string $alias, string $schema): \Edde\Entity\ICollection {
			$this->container->inject($collection = new Collection($this->storage->stream(new SelectQuery($schema = $this->schemaManager->load($schema), $alias))));
			$collection->schema($alias, $schema);
			return $collection;
		}

		/** @inheritdoc */
		public function execute(IEntityQueue $entityQueue): IEntityManager {
			$this->storage->execute(new QueryQueue($entityQueue));
			$entityQueue->commit();
			return $this;
		}
	}
