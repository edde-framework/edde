<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Schema\Exception\RelationException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;

		class Entity extends Crate implements IEntity {
			use EntityManager;
			use SchemaManager;
			use Storage;
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var IEntity[]
			 */
			protected $linkList = [];
			/**
			 * @var IEntity[]
			 */
			protected $relationList = [];

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				if (parent::isDirty()) {
					return true;
				}
				return false;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): ICrate {
				return parent::commit();
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimaryList(): array {
				$primaryList = [];
				foreach ($this->schema->getPrimaryList() as $property) {
					$primaryList[] = $this->getProperty($property->getName());
				}
				return $primaryList;
			}

			/**
			 * @inheritdoc
			 */
			public function link(IEntity $entity): IEntity {
				$this->linkList[] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkList(): array {
				return $this->linkList;
			}

			/**
			 * @inheritdoc
			 */
			public function save(): IEntity {
				$this->storage->save($this);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function update(): IEntity {
				$this->storage->update($this);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function insert(): IEntity {
				$this->storage->insert($this);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collection(): ICollection {
				return $this->storage->collection($this->schema->getName());
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity): IEntity {
				$relationList = $this->schema->getRelationList($schemaName = $entity->getSchema()->getName());
				if (count($relationList) === 0) {
					throw new RelationException(sprintf('There are no relations from [%s] to schema [%s].', $this->schema->getName(), $schemaName));
				} else if (count($relationList) !== 1) {
					throw new RelationException(sprintf('There are more relations from [%s] to schema [%s]. You have to specify relation schema.', $this->schema->getName(), $schemaName));
				}
				list($relation) = $relationList;
				$relationEntity = $this->entityManager->createEntity($relation->getSchema());
				$relationEntity->link($this);
				$relationEntity->link($entity);
				return $this->relationList[] = $relationEntity;
			}

			/**
			 * @inheritdoc
			 */
			public function getRelationList(): array {
				return $this->relationList;
			}

			public function __clone() {
				parent::__clone();
				$this->relationList = [];
			}
		}
