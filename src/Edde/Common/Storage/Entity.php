<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Exception\LinkException;
		use Edde\Api\Schema\Exception\RelationException;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;

		class Entity extends Crate implements IEntity {
			use EntityManager;
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
				$this->storage->execute($this->getQuery());
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
			public function getQuery(): IQuery {
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
			}
		}
