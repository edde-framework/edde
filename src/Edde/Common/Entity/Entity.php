<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Exception\LinkException;
		use Edde\Api\Schema\Exception\RelationException;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;
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
			public function link(IEntity $entity, ILink $link = null): IEntity {
				if ($link === null) {
					$linkList = $this->schema->getLinkList($schemaName = $entity->getSchema()->getName());
					if (($count = count($linkList)) === 0) {
						throw new LinkException(sprintf('There is no link from [%s] to [%s].', $this->schema->getName(), $schemaName));
					} else if ($count !== 1) {
						throw new LinkException(sprintf('There are more links from [%s] to [%s].', $this->schema->getName(), $schemaName));
					}
					list($link) = $linkList;
				}
				$this->set($link->getSourceProperty()->getName(), $entity->get($link->getTargetProperty()->getName()));
				$this->linkList[] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity): IEntity {
				$relationList = $this->schema->getRelationList($schemaName = $entity->getSchema()->getName());
				if (($count = count($relationList)) === 0) {
					throw new RelationException(sprintf('There are no relations from [%s] to schema [%s].', $this->schema->getName(), $schemaName));
				} else if ($count !== 1) {
					throw new RelationException(sprintf('There are more relations from [%s] to schema [%s]. You have to specify relation schema.', $this->schema->getName(), $schemaName));
				}
				list($relation) = $relationList;
				$relationEntity = $this->entityManager->createEntity($relation->getSchema());
				$relationEntity->link($this, $relation->getSourceLink());
				$relationEntity->link($entity, $relation->getTargetLink());
				return $this->relationList[] = $relationEntity;
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
					foreach ($this->relationList as $entity) {
						$entity->save();
					}
					foreach ($this->linkList as $entity) {
						$entity->save();
					}
					$source = $this->schemaManager->sanitize($this->schema, $this->toArray());
					$query = new InsertQuery($this->schema, $source);
					if ($this->exists) {
						$query = new UpdateQuery($this->schema, $source);
						$where = $query->where();
						foreach ($this->schema->getPrimaryList() as $name => $property) {
							$where->and()->eq($name)->to($this->get($name));
						}
					}
					$this->storage->execute($query);
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

			/**
			 * @inheritdoc
			 */
			public function commit(): ICrate {
				parent::commit();
				$this->linkList = [];
				$this->relationList = [];
				return $this;
			}

			public function __clone() {
				parent::__clone();
				$this->linkList = [];
				$this->relationList = [];
				$this->exists = false;
			}
		}
