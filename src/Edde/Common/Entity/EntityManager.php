<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\IEntityManager;
	use Edde\Api\Entity\IEntityQueue;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Entity\Query\QueryQueue;
	use Edde\Common\Object\Object;
	use Edde\Common\Storage\Query\SelectQuery;
	use Edde\Exception\Generator\UnknownGeneratorException;
	use Edde\Exception\Schema\UnknownPropertyException;
	use Edde\Exception\Schema\UnknownSchemaException;
	use Edde\Inject\Container\Container;
	use Edde\Inject\Schema\SchemaManager;
	use Edde\Inject\Storage\Storage;

	class EntityManager extends Object implements IEntityManager {
		use SchemaManager;
		use Storage;
		use Container;
		/** @var IEntity[] */
		protected $entities = [];

		/** @inheritdoc */
		public function createEntity(ISchema $schema): IEntity {
			if (isset($this->entities[$name = $schema->getName()]) === false) {
				$this->entities[$name] = $this->container->inject(new Entity($schema));
			}
			return clone $this->entities[$name];
		}

		/**
		 * @inheritdoc
		 *
		 * @throws UnknownGeneratorException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 */
		public function create(string $schema, array $source = []): IEntity {
			return $this->createEntity($schema = $this->schemaManager->load($schema))->put($this->schemaManager->generate($schema, $source));
		}

		/** @inheritdoc */
		public function load(ISchema $schema, array $source): IEntity {
			$entity = $this->createEntity($schema);
			$entity->filter($source);
			return $entity;
		}

		/** @inheritdoc */
		public function collection(string $alias, string $schema): ICollection {
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
