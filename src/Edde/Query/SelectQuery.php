<?php
	declare(strict_types=1);
	namespace Edde\Query;

	class SelectQuery extends AbstractQuery implements ISelectQuery {
		protected $uses = [];
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
				$this->use($schema, (string)$alias);
			}
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
	}
