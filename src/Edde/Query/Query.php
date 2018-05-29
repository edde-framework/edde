<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use stdClass;
	use function is_string;

	class Query extends SimpleObject implements IQuery {
		/** @var string[] */
		protected $selects = [];
		/** @var stdClass[] */
		protected $attaches = [];
		/** @var bool[] */
		protected $attached = [];
		/** @var IWheres */
		protected $wheres;
		/** @var IGroup[] */
		protected $groups = [];
		/** @var stdClass[] */
		protected $orders = [];
		/** @var stdClass */
		protected $page;
		/** @var string[] */
		protected $returns = [];
		/** @var array */
		protected $params = [];
		protected $count = false;

		/** @inheritdoc */
		public function select(string $schema, string $alias = null): IQuery {
			$this->selects[$alias ?: $schema] = $schema;
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
			return $this->wheres ?: $this->wheres = new Wheres();
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
			foreach ($aliases as $alias) {
				$this->return($alias);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getSelect(string $alias): string {
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
		public function params(array $params): IQuery {
			$this->params = $params;
			return $this;
		}

		/** @inheritdoc */
		public function getParams(): array {
			return $this->params;
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
	}
