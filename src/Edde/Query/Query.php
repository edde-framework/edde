<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Schema\ISchema;
	use Edde\SimpleObject;
	use stdClass;
	use function array_keys;
	use function implode;
	use function is_string;

	class Query extends SimpleObject implements IQuery {
		/** @var IParams */
		protected $params;
		/** @var ISchema[] */
		protected $selects = [];
		/** @var stdClass[] */
		protected $attaches = [];
		/** @var bool[] */
		protected $attached = [];
		/** @var IWheres */
		protected $wheres;
		/** @var stdClass[] */
		protected $orders = [];
		/** @var stdClass */
		protected $page;
		/** @var string[] */
		protected $returns = [];
		/** @var ISchema[] */
		protected $schemas;
		/** @var bool */
		protected $count = false;
		/** @var IQuery[] */
		protected $queries = [];

		public function __construct() {
			$this->params = new Params();
		}

		/** @inheritdoc */
		public function select(ISchema $schema, string $alias = null): IQuery {
			$this->selects[$alias ?: $schema->getName()] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function selects(array $schemas): IQuery {
			foreach ($schemas as $alias => $schema) {
				$this->select($schema, is_string($alias) ? $alias : null);
			}
			return $this;
		}

		/** @inheritdoc */
		public function attach(string $attach, string $to, string $relation): IQuery {
			$this->attaches[] = (object)[
				'attach'   => $attach,
				'to'       => $to,
				'relation' => $relation,
			];
			$this->attached[$attach] = $this->attached[$to] = $this->attached[$relation] = true;
			return $this;
		}

		/** @inheritdoc */
		public function hasAttaches(): bool {
			return empty($this->attaches) === false;
		}

		/** @inheritdoc */
		public function isAttached(string $alias): bool {
			return isset($this->attached[$alias]);
		}

		/** @inheritdoc */
		public function getAttaches(): array {
			return $this->attaches;
		}

		/** @inheritdoc */
		public function wheres(): IWheres {
			return $this->wheres ?: $this->wheres = new Wheres($this->params);
		}

		/** @inheritdoc */
		public function order(string $alias, string $property, string $order = 'asc'): IQuery {
			$this->orders[] = (object)[
				'alias'    => $alias,
				'property' => $property,
				'order'    => $order,
			];
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
		public function page(int $page, int $size): IQuery {
			$this->page = (object)[
				'page' => $page,
				'size' => $size,
			];
			return $this;
		}

		/** @inheritdoc */
		public function hasPage(): bool {
			return $this->page !== null;
		}

		/** @inheritdoc */
		public function getPage(): stdClass {
			return $this->page;
		}

		/** @inheritdoc */
		public function return(string $alias): IQuery {
			$this->returns[$alias] = $alias;
			return $this;
		}

		/** @inheritdoc */
		public function returns(array $aliases): IQuery {
			$this->returns = [];
			foreach ($aliases as $alias) {
				$this->return($alias);
			}
			return $this;
		}

		/** @inheritdoc */
		public function just(string $alias, string $property, string $name = null): IQuery {
			$this->returns = [
				(object)[
					'alias'    => $alias,
					'property' => $property,
					'name'     => $name ?: $alias,
				],
			];
			return $this;
		}

		/** @inheritdoc */
		public function getReturns(): array {
			return empty($this->returns) ? array_keys($this->selects) : $this->returns;
		}

		/** @inheritdoc */
		public function getSchema(string $alias): ISchema {
			if (isset($this->selects[$alias]) === false) {
				throw new QueryException(sprintf('Requested alias [%s] is not available in the query.', $alias));
			}
			return $this->selects[$alias];
		}

		/** @inheritdoc */
		public function getSelects(): array {
			return $this->selects;
		}

		/** @inheritdoc */
		public function getSchemas(): array {
			if ($this->schemas) {
				return $this->schemas;
			}
			$this->schemas = [];
			foreach ($this->selects as $schema) {
				$this->schemas[$schema->getName()] = $schema;
			}
			return $this->schemas;
		}

		/** @inheritdoc */
		public function getParams(): IParams {
			return $this->params;
		}

		/** @inheritdoc */
		public function params(array $bind): array {
			return $this->params->params($bind);
		}

		/** @inheritdoc */
		public function count(bool $count = true): IQuery {
			$this->count = $count;
			return $this;
		}

		/** @inheritdoc */
		public function isCount(): bool {
			return $this->count;
		}

		/** @inheritdoc */
		public function query(string $name): IQuery {
			return $this->queries[$name] = new Query();
		}

		/** @inheritdoc */
		public function getQuery(string $name): IQuery {
			if (isset($this->queries[$name]) === false) {
				throw new QueryException(sprintf('Requested unknown sub-query [%s]; available queries are [%s].', $name, implode(', ', array_keys($this->queries))));
			}
			return $this->queries[$name];
		}

		/** @inheritdoc */
		public function getQueries(): array {
			return $this->queries;
		}
	}
