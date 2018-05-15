<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use function is_string;

	class SelectQuery extends AbstractQuery implements ISelectQuery {
		protected $uses = [];
		protected $attaches = [];
		protected $wheres = [];
		protected $orders = [];
		protected $returns = [];

		public function __construct() {
			parent::__construct('select');
		}

		/** @inheritdoc */
		public function use(string $schema, string $alias = null): ISelectQuery {
			$this->uses[$alias ?: $schema] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function uses(array $schemas): ISelectQuery {
			foreach ($schemas as $alias => $schema) {
				$this->use($schema, is_string($alias) ? $alias : null);
			}
			return $this;
		}

		/** @inheritdoc */
		public function attach(string $attach, string $to, string $relation): ISelectQuery {
			$this->attaches[] = (object)[
				'attach'   => $attach,
				'to'       => $to,
				'relation' => $relation,
			];
			return $this;
		}

		/** @inheritdoc */
		public function equal(string $source, string $from, string $target, string $to): ISelectQuery {
			$this->wheres[] = (object)[
				'type'   => 'equal',
				'source' => (object)['alias' => $source, 'property' => $from],
				'target' => (object)['alias' => $target, 'property' => $to],
			];
			return $this;
		}

		/** @inheritdoc */
		public function equalTo(string $alias, string $property, $value): ISelectQuery {
			$this->wheres[] = (object)[
				'type'  => 'equalTo',
				'alias' => $alias,
				'value' => $value,
			];
			return $this;
		}

		/** @inheritdoc */
		public function order(string $alias, string $property, string $order = 'asc'): ISelectQuery {
			$this->orders[] = (object)[
				'alias'    => $alias,
				'property' => $property,
				'order'    => $order,
			];
			return $this;
		}

		/** @inheritdoc */
		public function return(string $alias): ISelectQuery {
			$this->returns[$alias] = $alias;
			return $this;
		}

		/** @inheritdoc */
		public function returns(array $aliases): ISelectQuery {
			foreach ($aliases as $alias) {
				$this->return($alias);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getSchema(string $alias): string {
			if (isset($this->uses[$alias]) === false) {
				throw new QueryException(sprintf('Requested alias [%s] is not available in the query.', $alias));
			}
			return $this->uses[$alias];
		}

		/** @inheritdoc */
		public function getSchemas(): array {
			return $this->uses;
		}
	}
