<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query;

	use Edde\Api\Storage\Query\Fragment\IJoin;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Api\Storage\Query\ISelectQuery;
	use Edde\Common\Storage\Query\Fragment\Join;
	use Edde\Common\Storage\Query\Fragment\WhereGroup;
	use Edde\Exception\Storage\UnknownAliasException;
	use Edde\Schema\ISchema;

	class SelectQuery extends AbstractQuery implements ISelectQuery {
		/** @var string */
		protected $alias;
		/** @var IJoin[] */
		protected $joins = [];
		/** @var IWhereGroup */
		protected $where;
		/** @var string[] */
		protected $orders = [];
		protected $limit;
		/** @var string */
		protected $count;
		/** @var \Edde\Schema\ISchema[] */
		protected $schemas;

		public function __construct(ISchema $schema, string $alias) {
			$this->alias = $alias;
			$this->schemas[null] = $this->schemas[$alias] = $schema;
		}

		/** @inheritdoc */
		public function link(string $alias, string $schema): ISelectQuery {
			$this->joins[$alias] = new Join($schema, $alias, true);
			return $this;
		}

		/** @inheritdoc */
		public function join(string $alias, string $schema, string $relation = null): ISelectQuery {
			$this->joins[$alias] = new Join($schema, $alias, false, $relation);
			return $this;
		}

		/** @inheritdoc */
		public function getJoins(): array {
			return $this->joins;
		}

		/** @inheritdoc */
		public function getSchemas(): array {
			return $this->schemas;
		}

		/** @inheritdoc */
		public function where(string $name, string $expression, $value = null): ISelectQuery {
			if (($dot = strpos($name, '.')) === false) {
				$name = $this->alias . '.' . $name;
			}
			$this->getWhere()->and()->expression($name, $expression, $value);
			return $this;
		}

		/** @inheritdoc */
		public function hasWhere(): bool {
			return $this->where !== null;
		}

		/** @inheritdoc */
		public function getWhere(): IWhereGroup {
			return $this->where ?: $this->where = new WhereGroup();
		}

		/** @inheritdoc */
		public function order(string $name, bool $asc = true): ISelectQuery {
			if (($dot = strpos($name, '.')) === false) {
				$name = $this->alias . '.' . $name;
			}
			$this->orders[$name] = $asc;
			return $this;
		}

		/** @inheritdoc */
		public function hasOrder(): bool {
			return empty($this->orders) === false;
		}

		/** @inheritdoc */
		public function getOrders(): array {
			return $this->orders;
		}

		/** @inheritdoc */
		public function limit(int $limit, int $page): ISelectQuery {
			$this->limit = [$limit, $page];
			return $this;
		}

		/** @inheritdoc */
		public function hasLimit(): bool {
			return $this->limit !== null;
		}

		/** @inheritdoc */
		public function getLimit(): array {
			return $this->limit;
		}

		/** @inheritdoc */
		public function count(string $alias = null): ISelectQuery {
			$this->count = $alias;
			return $this;
		}

		/** @inheritdoc */
		public function isCount(): bool {
			return $this->count !== null;
		}

		/** @inheritdoc */
		public function getCount(): string {
			return $this->count;
		}

		/** @inheritdoc */
		public function alias(string $alias, ISchema $schema): ISelectQuery {
			$this->schemas[$alias] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function getAlias(): string {
			return $this->alias;
		}

		/** @inheritdoc */
		public function getSchema(string $alias = null): ISchema {
			if (isset($this->schemas[$alias]) === false) {
				throw new UnknownAliasException(sprintf('Requested unknown alias [%s] in query.', $alias));
			}
			return $this->schemas[$alias];
		}
	}
