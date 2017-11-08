<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Crate\IProperty;
		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Entity\Inject\Transaction;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;

		class Entity extends Crate implements IEntity {
			use EntityManager;
			use SchemaManager;
			use Transaction;
			use Storage;
			/**
			 * @var ISchema
			 */
			protected $schema;
			protected $saving = false;
			/**
			 * @var IProperty
			 */
			protected $primary = null;

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
			public function getPrimary(): IProperty {
				return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
			}

			/**
			 * @inheritdoc
			 */
			public function filter(array $source): IEntity {
				$this->push($this->schemaManager->filter($this->schema, $source));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function linkTo(IEntity $entity): IEntity {
				$link = $this->schema->getLink($entity->getSchema()->getName());
				$this->transaction->link($this, $entity, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias): ICollection {
				$collection = $this->entityManager->collection($this->schema->getName());
				$collection->link($schema, $alias, $this->toArray());
				return $collection;
			}

			/**
			 * @inheritdoc
			 */
			public function unlink(string $schema): IEntity {
				$this->transaction->unlink($this, $this->schema->getLink($schema));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity): IEntity {
				$relation = $this->schema->getRelation($entity->getSchema()->getName());
				$use = $this->entityManager->create($relation->getSchema()->getName());
				$this->transaction->attach($this, $entity, $use, $relation);
				return $use;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ICollection {
				$collection = $this->entityManager->collection($this->schema->getName());
				$collection->join($schema, $alias, $this->toArray());
				return $collection;
			}

			/**
			 * @inheritdoc
			 */
			public function toArray(): array {
				$array = [];
				foreach ($this->schema->getPropertyList() as $k => $property) {
					$array[$k] = $this->get($k);
				}
				return $array;
			}

			/**
			 * @inheritdoc
			 */
			public function __clone() {
				parent::__clone();
				$this->primary = null;
			}
		}
