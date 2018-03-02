<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Entity\Exception\RecordException;
	use Edde\Api\Entity\Exception\UnknownAliasException;
	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\IRecord;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Exception\EntityNotFoundException;
	use Edde\Api\Storage\IStream;
	use Edde\Api\Storage\Query\ISelectQuery;
	use Edde\Common\Object\Object;
	use Edde\Common\Storage\Query\SelectQuery;

	class Collection extends Object implements ICollection {
		use SchemaManager;
		use Container;
		/** @var IStream */
		protected $stream;
		/** @var ISchema[] */
		protected $schemas;

		public function __construct(IStream $stream) {
			$this->stream = $stream;
		}

		/** @inheritdoc */
		public function schema(string $alias, ISchema $schema): ICollection {
			$this->schemas[$alias] = $schema;
			$this->stream->getQuery()->alias($alias, $schema);
			return $this;
		}

		/** @inheritdoc */
		public function getSchema(string $alias): ISchema {
			if (isset($this->schemas[$alias]) === false) {
				throw new UnknownAliasException(sprintf('Requested schema for unknown alias [%s].', $alias));
			}
			return $this->schemas[$alias];
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
		public function getEntity(string $alias): IEntity {
			/** @var $record IRecord */
			foreach ($this as $record) {
				return $record->getEntity($alias);
			}
			throw new EntityNotFoundException('Cannot load any Entity for Collection.');
		}

		/** @inheritdoc */
		public function getRecord(): IRecord {
			foreach ($this as $record) {
				return $record;
			}
			throw new RecordException('Cannot load any Record for Collection.');
		}

		/** @inheritdoc */
		public function entity(string $alias, $name): IEntity {
			$schema = $this->getSchema($alias);
			$this->stream->query($query = new SelectQuery($schema, $alias));
			$where = $query->getWhere();
			if ($schema->hasPrimary()) {
				$where->or()->value($alias . '.' . $schema->getPrimary()->getName(), '=', $name);
			}
			foreach ($schema->getUniques() as $property) {
				$where->or()->value($alias . '.' . $property->getName(), '=', $name);
			}
			return $this->getEntity($alias);
		}

		/** @inheritdoc */
		public function join(string $source, string $target, string $alias, array $on = null): ICollection {
			$schema = $this->getSchema($source);
			$relation = $schema->getRelation($target);
			$query = $this->stream->getQuery();
			$query->join($alias, $target);
			if ($on) {
				$propertyName = $relation->getTo()->getTo()->getPropertyName();
				$query->where($source . '.' . $propertyName, '=', $on[$propertyName]);
			}
			$this->schema($source . '\r', $relation->getSchema());
			$this->schema($alias, $this->schemaManager->load($target));
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
		public function link(string $alias, string $schema): ICollection {
			$this->getQuery()->link($alias, $schema);
			$this->schema($alias, $this->schemaManager->load($schema));
			return $this;
		}

		/** @inheritdoc */
		public function count(string $alias): int {
			$query = $this->stream->getQuery();
			$query->count($alias ?: $query->getAlias());
			$count = 0;
			foreach ($this->stream as $count) {
				$count = $count[$alias];
				$count = (int)(is_int($count) ? $count : (isset($count['count']) ? $count['count'] : 0));
				break;
			}
			$query->count(null);
			return $count;
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach ($this->stream as $source) {
				yield $this->container->inject(new Record($this->schemas, $source));
			}
		}
	}
