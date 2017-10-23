<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Filter\IFilter;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Common\Object\Object;

		class EntityManager extends Object implements IEntityManager {
			use SchemaManager;
			/**
			 * @var IFilter[]
			 */
			protected $generatorList = [];

			/**
			 * @inheritdoc
			 */
			public function registerGenerator(string $name, IFilter $filter): IEntityManager {
				$this->generatorList[$name] = $filter;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerGeneratorList(array $filterList): IEntityManager {
				foreach ($filterList as $name => $filter) {
					$this->registerGenerator($name, $filter);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function create(string $schema, array $source = []): IEntity {
				$entity = new Entity($this->schemaManager->getSchema($schema));
				$entity->update($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(IEntity $entity): bool {
				return $entity->isDirty();
			}

			/**
			 * @inheritdoc
			 */
			public function getDirtyProperties(IEntity $entity): array {
				return $entity->getDirtyProperties();
			}
		}
