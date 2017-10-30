<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityException;
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
				foreach ($this->linkList as $entity) {
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
				$this->linkList = [];
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
			public function attach(string $name, IEntity $entity): IEntity {
				if ($this->schema->getProperty($name)->isLink() === false) {
					throw new EntityException(sprintf('Cannot attach entity [%s] to entity [%s::%s] - property is not a link.', $entity->getSchema()->getName(), $this->schema->getName(), $name));
				}
				$this->linkList[$name] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function hasLinks(): bool {
				return empty($this->linkList) === false;
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
			public function relationTo(IEntity $entity, string $schema): IEntity {
				return $this->entityManager->attach($this, $entity, $schema);
			}

			/**
			 * @inheritdoc
			 */
			public function relationOf(string $schema, string $relation): ICollection {
				$collection = $this->storage->collection($schema);
				$relationSchema = $this->schemaManager->getSchema($relation);
				list($targetProperty) = $relationSchema->getRelationList($schema);
				list($sourceProperty) = $relationSchema->getRelationList($this->schema->getName());
				$targetLink = $targetProperty->getLink();
				$sourceLink = $sourceProperty->getLink();
				$collection->table($relation, 'r')->where()->eq($sourceLink->getProperty(), 'r')->to($this->get($sourceLink->getTarget()))->and()->eq($targetLink->getProperty(), 'r')->toColumn($targetLink->getTarget(), 'c');
				return $collection;
			}

			public function __clone() {
				parent::__clone();
				$this->linkList = [];
			}
		}
