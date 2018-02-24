<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\IEntityManager;
	use Edde\Api\Entity\IEntityQueue;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Inject\Storage;
	use Edde\Common\Entity\Query\QueryQueue;
	use Edde\Common\Object\Object;
	use Edde\Common\Storage\Query\SelectQuery;

	class EntityManager extends Object implements IEntityManager {
		use SchemaManager;
		use Container;
		use Storage;
		/** @var IEntity[] */
		protected $entities = [];
		/** @var ICollection[] */
		protected $collections = [];

		/**
		 * @inheritdoc
		 */
		public function createEntity(ISchema $schema): IEntity {
			if (isset($this->entities[$name = $schema->getName()]) === false) {
				$this->entities[$name] = $this->container->inject(new Entity($schema));
			}
			return clone $this->entities[$name];
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
		public function load(ISchema $schema, array $source): IEntity {
			$entity = $this->createEntity($schema);
			$entity->filter($source);
			return $entity;
		}

		/**
		 * @inheritdoc
		 */
		public function collection(string $schema): ICollection {
			if (isset($this->collections[$name = $schema]) === false) {
				$this->collections[$name] = $this->container->inject(new Collection($this->storage->stream(new SelectQuery($schema = $this->schemaManager->load($schema), 'c')), $schema));
			}
			return clone $this->collections[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IEntityQueue $entityQueue): IEntityManager {
			$this->storage->execute(new QueryQueue($entityQueue));
			$entityQueue->commit();
			return $this;
		}
	}
