<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Inject\EntityManager;
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

			public function __construct(IStream $stream, ISchema $schema) {
				$this->stream = $stream;
				$this->schema = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function query(ISelectQuery $query): ICollection {
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
				$this->stream->query($query = new SelectQuery($this->schema, 'c'));
				$where = $query->getTable()->where();
				if ($this->schema->hasPrimary()) {
					$where->or()->value('c.' . $this->schema->getPrimary()->getName(), '=', $name);
				}
				foreach ($this->schema->getUniqueList() as $property) {
					$where->or()->value('c.' . $property->getName(), '=', $name);
				}
				return $this->getEntity();
			}

			/**
			 * @inheritdoc
			 */
			public function link(string $schema, string $alias, array $source): ICollection {
				$this->stream->getQuery()->link($schema, $alias, $source)->return();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function join(string $target, string $alias, array $on = null): ICollection {
				/**
				 * change target schema of this collection
				 */
				$this->schema = ($link = ($relation = $this->schema->getRelation($target))->getTo())->getTo()->getSchema();
				($query = $this->stream->getQuery())->join($target, $alias);
				if ($on) {
					$query->where($query->getTable()->getAlias() . '.' . ($name = $link->getTo()->getPropertyName()), '=', $on[$name]);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function where(string $name, string $relation, $value): ICollection {
				$this->stream->getQuery()->where($name, $relation, $value);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function order(string $name, bool $asc = true): ICollection {
				$this->stream->getQuery()->order($name, $asc);
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
			}
		}
