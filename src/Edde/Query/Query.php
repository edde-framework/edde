<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Object;

	class Query extends Object implements IQuery {
		protected $uses = [];

		/** @inheritdoc */
		public function use(string $schema, string $alias = null): IQuery {
			$this->uses[$alias ?: $schema] = $schema;
			return $this;
		}

		/** @inheritdoc */
		public function uses(array $schemas): IQuery {
			foreach ($schemas as $alias => $schema) {
				$this->use($schema, (string)$alias);
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
