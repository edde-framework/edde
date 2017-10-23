<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Filter\IFilter;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\UnknownGeneratorException;
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
				$entity = $this->createEntity($schema);
				$entity->update($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function factory(string $schema, array $source): IEntity {
				$entity = $this->createEntity($schema);
				$entity->push($source);
				return $entity;
			}

			/**
			 * @inheritdoc
			 */
			public function createEntity(string $schema): IEntity {
				return new Entity($this->schemaManager->getSchema($schema));
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

			/**
			 * @inheritdoc
			 */
			public function generate(IEntity $entity): IEntityManager {
				foreach (($schema = $entity->getSchema())->getGeneratorList() as $property) {
					if (($filter = $this->generatorList[$generator = $property->getGenerator()] ?? null) === null) {
						throw new UnknownGeneratorException(sprintf('Unknown generator [%s] for property [%s::%s].', $generator, $schema->getName(), $property->getName()));
					} else if (($value = $entity->getProperty($property->getName()))->isEmpty() === false) {
						continue;
					}
					$value->setValue($filter->filter(null));
				}
				return $this;
			}
		}
