<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Exception\RelationException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Crate\Crate;
		use Edde\Common\Query\InsertQuery;
		use Edde\Common\Query\UpdateLinkQuery;
		use Edde\Common\Query\UpdateQuery;
		use Edde\Common\Query\UpdateRelationQuery;

		class Entity extends Crate implements IEntity {
			use EntityManager;
			use SchemaManager;
			use Storage;
			/**
			 * @var ISchema
			 */
			protected $schema;
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
			public function save(): IEntity {
				$isRelation = $this->schema->isRelation();
				if ($this->saving) {
					return $this;
				} else if ($this->isDirty() === false && $isRelation === false) {
					return $this;
				}
				try {
					$this->saving = true;
					/**
					 * linked entities must be saved first as they must exists before
					 * relations could be created
					 */
					foreach ($this->bindList as $entity) {
						$entity->save();
					}
					$linkList = [];
					$source = $this->toArray();
					foreach ($this->linkList as $entity) {
						$entity->save();
						$linkList[] = $query = new UpdateLinkQuery($link = $this->schema->getLink($entity->getSchema()->getName()));
						$query->from($source);
						$query->to($entity->toArray());
						$this->set($name = $link->getSourceProperty()->getName(), $value = $entity->get($link->getTargetProperty()->getName()));
						$source[$name] = $value;
					}
					$query = new InsertQuery($this->schema, $source);
					if ($isRelation) {
						if (count($this->bindList) !== 2) {
							throw new RelationException(sprintf('Cannot save [%s] as it does not have exactly two links', $this->schema->getName()));
						}
						list($from, $to) = $this->bindList;
						$query = new UpdateRelationQuery($this->relation, $source);
						$query->from($from->toArray());
						$query->to($to->toArray());
					} else if ($this->exists) {
						$query = new UpdateQuery($this->schema, $source);
					}
					$this->storage->execute($query);
					/**
					 * relations must be saved last as all related entities already exists
					 */
					foreach ($this->bindToList as $entity) {
						$entity->save();
					}
					foreach ($linkList as $query) {
						$this->storage->execute($query);
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
			public function filter(array $source): IEntity {
				$this->push($this->schemaManager->filter($this->schema, $source));
				$this->exists = true;
				return $this;
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
			public function exists(): bool {
				return $this->exists;
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
				$this->exists = false;
			}
		}
