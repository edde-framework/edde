<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\IStream;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\SelectQuery;

		class Collection extends Object implements ICollection {
			use EntityManager;
			use SchemaManager;
			/**
			 * @var IStream
			 */
			protected $stream;
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var ITable
			 */
			protected $table;
			/**
			 * @var ISchema
			 */
			protected $join;

			public function __construct(IStream $stream, ISchema $schema) {
				$this->stream = $stream;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function query(ISelectQuery $query): ICollection {
				$this->table = null;
				$this->join = null;
				$this->stream->query($query);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): ISelectQuery {
				return $this->stream->getQuery();
			}

			/**
			 * @inheritdoc
			 */
			public function getEntity(): IEntity {
				foreach ($this as $entity) {
					return $entity;
				}
				throw new EntityNotFoundException(sprintf('Cannot load any Entity by query [%s].', $this->stream->getQuery()->getDescription()));
			}

			/**
			 * @inheritdoc
			 */
			public function entity($name): IEntity {
				$this->stream->query($query = new SelectQuery());
				$where = $query->table($this->schema, 'c')->select()->where();
				if ($this->schema->hasPrimary()) {
					$where->or()->eq($this->schema->getPrimary()->getName())->to($name);
				}
				foreach ($this->schema->getUniqueList() as $property) {
					$where->or()->eq($property->getName())->to($name);
				}
				return $this->getEntity();
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $target, string $alias, array $on = []): ICollection {
				if ($this->join === null) {
					$this->join = $this->schema;
				}
				if ($this->table === null) {
					$this->table = $this->stream->getQuery()->table($this->schema, 'c');
				}
				$this->table->join($relation = $this->join->getRelation($target), $alias, $on);
				$this->join = $relation->getTargetLink()->getTargetSchema();
				$this->schema = $this->schemaManager->load($target);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				foreach ($this->stream as $source) {
					yield $this->entityManager->load($this->schema, $source);
				}
			}

			public function __clone() {
				parent::__clone();
				$this->stream = clone $this->stream;
				$this->table = null;
				$this->join = $this->schema;
			}
		}
