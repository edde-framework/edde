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
				$this->transaction->link($this, $entity, $link = $this->schema->getLink($entity->getSchema()->getName()));
				$this->set($link->getFrom()->getPropertyName(), $entity->get($link->getTo()->getPropertyName()));
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias): ICollection {
				return $this->entityManager->collection($this->schema->getName())->link($schema, $alias, $this->toArray());
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $schema, string $alias): ICollection {
				return $this->entityManager->collection($this->schema->getName())->join($schema, $alias, $this->toArray());
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
