<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Exception\RelationException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;
		use Edde\Common\Query\CreateRelationQuery;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\UpdateQuery;

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
			/**
			 * @var IRelation
			 */
			protected $relation;
			protected $exists = false;
			protected $saving = false;

			public function __construct(ISchema $schema) {
				$this->schema = $schema;
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
			public function attach(IEntity $entity): IEntity {
				if (($count = count($relationList = $this->schema->getRelationList($schemaName = $entity->getSchema()->getName()))) === 0) {
					throw new RelationException(sprintf('There are no relations from [%s] to schema [%s].', $this->schema->getName(), $schemaName));
				} else if ($count !== 1) {
					throw new RelationException(sprintf('There are more relations from [%s] to schema [%s]. You have to specify relation schema.', $this->schema->getName(), $schemaName));
				}
				return $this->entityManager->createEntity($relationList[0]->getSchema())->connect($this, $entity, $relationList[0]);
			}

			/**
			 * @inheritdoc
			 */
			public function connect(IEntity $entity, IEntity $to, IRelation $relation): IEntity {
				$this->relation = $relation;
				$entity->relation($this);
				$entity->link($to);
				$this->link($entity);
				$this->link($to);
				return $this;
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
			public function relation(IEntity $entity): IEntity {
				$this->relationList[] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function save(): IEntity {
				if ($this->saving) {
					return $this;
				} else if ($this->isDirty() === false) {
					return $this;
				}
				try {
					$this->saving = true;
					/**
					 * linked entities must be saved first as they must exists before
					 * relations could be created
					 */
					foreach ($this->linkList as $entity) {
						$entity->save();
					}
					$query = new InsertQuery($this->schema, $source = $this->toArray());
					if ($this->schema->isRelation()) {
						if (count($this->linkList) !== 2) {
							throw new RelationException(sprintf('Cannot save [%s] as it does not have exactly two links', $this->schema->getName()));
						}
						list($from, $to) = $this->linkList;
						$query = new CreateRelationQuery($this->relation);
						$query->from($from->toArray());
						$query->to($to->toArray());
					} else if ($this->exists) {
						$query = new UpdateQuery($this->schema, $source);
						$where = $query->where();
						foreach ($this->schema->getPrimaryList() as $name => $property) {
							$where->and()->eq($name)->to($this->get($name));
						}
					}
					$this->storage->execute($query);
					/**
					 * relations must be saved last as all related entities already exists
					 */
					foreach ($this->relationList as $entity) {
						$entity->save();
					}
					$this->commit();
					$this->exists = true;
					return $this;
				} finally {
					$this->saving = false;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function load(array $source): IEntity {
				$this->push($this->schemaManager->filter($this->schema, $source));
				$this->exists = true;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function collectionOf(string $schema): ICollection {
				$collection = $this->entityManager->collection($schema);
				if (($count = count($relationList = $this->schema->getRelationList($schema))) === 0) {
					throw new RelationException(sprintf('There are no relations from [%s] to schema [%s].', $this->schema->getName(), $schema));
				} else if ($count !== 1) {
					throw new RelationException(sprintf('There are more relations from [%s] to schema [%s]. You have to specify relation schema.', $this->schema->getName(), $schema));
				}
				$query = $collection->getQuery();
				$query->schema($this->schema->getName(), 'a')->link($relationList[0], 'r')->source($this->toArray());
				return $collection;
			}

			/**
			 * @inheritdoc
			 */
			public function exists(): bool {
				return $this->exists;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				if (parent::isDirty()) {
					return true;
				}
				foreach ($this->linkList as $entity) {
					if ($entity->isDirty()) {
						return true;
					}
				}
				foreach ($this->relationList as $entity) {
					if ($entity->isDirty()) {
						return true;
					}
				}
				return false;
			}

			public function toArray(): array {
				$array = [];
				foreach ($this->schema->getPropertyList() as $k => $property) {
					$array[$k] = $this->get($k);
				}
				return $array;
			}

			public function __clone() {
				parent::__clone();
				$this->linkList = [];
				$this->relationList = [];
				$this->relation = null;
				$this->exists = false;
			}
		}
