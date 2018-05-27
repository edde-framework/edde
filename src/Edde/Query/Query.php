<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function is_string;

	class Query extends SimpleObject implements IQuery {
		/** @var string[] */
		protected $selects = [];
		protected $attaches = [];
		protected $wheres = [];
		protected $orders = [];
		protected $returns = [];

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
			return $this;
		}

		/** @inheritdoc */
		public function equal(string $source, string $from, string $target, string $to): IQuery {
			$this->wheres[] = (object)[
				'type'   => 'equal',
				'source' => (object)['alias' => $source, 'property' => $from],
				'target' => (object)['alias' => $target, 'property' => $to],
			];
			return $this;
		}

		/** @inheritdoc */
		public function equalTo(string $alias, string $property, $value): IQuery {
			$this->wheres[] = (object)[
				'type'  => 'equalTo',
				'alias' => $alias,
				'value' => $value,
			];
			return $this;
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
	}
