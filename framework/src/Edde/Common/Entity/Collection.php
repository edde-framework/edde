<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Inject\EntityManager;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Exception\EntityNotFoundException;
	use Edde\Api\Storage\IStream;
	use Edde\Api\Storage\Query\ISelectQuery;
	use Edde\Common\Object\Object;
	use Edde\Common\Storage\Query\SelectQuery;

	class Collection extends Object implements ICollection {
		use EntityManager;
		use SchemaManager;
		/** @var IStream */
		protected $stream;
		/** @var ISchema */
		protected $schema;

		public function __construct(IStream $stream, ISchema $schema) {
			$this->stream = $stream;
			$this->schema = $schema;
		}

		/** @inheritdoc */
		public function query(ISelectQuery $query): ICollection {
			$this->stream->query($query);
			return $this;
		}

		/** @inheritdoc */
		public function getQuery(): ISelectQuery {
			return $this->stream->getQuery();
		}

		/** @inheritdoc */
		public function getEntity(): IEntity {
			foreach ($this as $entity) {
				return $entity;
			}
			throw new EntityNotFoundException(sprintf('Cannot load any Entity of schema [%s].', $this->schema->getName()));
		}

		/** @inheritdoc */
		public function entity($name): IEntity {
			$this->stream->query($query = new SelectQuery($this->schema, 'c'));
			$where = $query->getWhere();
			if ($this->schema->hasPrimary()) {
				$where->or()->value('c.' . $this->schema->getPrimary()->getName(), '=', $name);
			}
			foreach ($this->schema->getUniques() as $property) {
				$where->or()->value('c.' . $property->getName(), '=', $name);
			}
			return $this->getEntity();
		}

		/** @inheritdoc */
		public function join(string $target, string $alias, array $on = null): ICollection {
			$relation = $this->schema->getRelation($target);
			$link = $relation->getTo();
			$linkTo = $link->getTo();
			/**
			 * change target schema of this collection
			 */
			$this->schema = $linkTo->getSchema();
			$query = $this->stream->getQuery();
			$query->join($target, $alias);
			if ($on) {
				$propertyName = $linkTo->getPropertyName();
				$query->where($query->getAlias() . '.' . $propertyName, '=', $on[$propertyName]);
			}
			return $this;
		}

		/** @inheritdoc */
		public function where(string $name, string $relation, $value): ICollection {
			$this->stream->getQuery()->where($name, $relation, $value);
			return $this;
		}

		/** @inheritdoc */
		public function order(string $name, bool $asc = true): ICollection {
			$this->stream->getQuery()->order($name, $asc);
			return $this;
		}

		/** @inheritdoc */
		public function orderAsc(string $name): ICollection {
			return $this->order($name);
		}

		/** @inheritdoc */
		public function orderDesc(string $name): ICollection {
			return $this->order($name, false);
		}

		/** @inheritdoc */
		public function limit(int $limit, int $page): ICollection {
			$this->stream->getQuery()->limit($limit, $page);
			return $this;
		}

		/** @inheritdoc */
		public function count(): int {
			$this->stream->getQuery()->count();
			$count = 0;
			foreach ($this->stream as $count) {
				$count = (int)(is_int($count) ? $count : (isset($count['count']) ? $count['count'] : 0));
				break;
			}
			$this->stream->getQuery()->count(false);
			return $count;
		}

		/** @inheritdoc */
		public function return(string $alias = null): ICollection {
			$this->stream->getQuery()->return($alias);
			return $this;
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach ($this->stream as $source) {
				yield $this->entityManager->load($this->schema, $source);
			}
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->stream = clone $this->stream;
		}
	}
