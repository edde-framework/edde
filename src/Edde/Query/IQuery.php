<?php
	declare(strict_types=1);
	namespace Edde\Query;

	/**
	 * Formal marker interface for a query; the general purpose
	 * of a query is to get data from a storage, it's not intended
	 * for other usage.
	 */
	interface IQuery {
		/**
		 * add a source to the query
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return IQuery
		 */
		public function use(string $schema, string $alias = null): IQuery;

		/**
		 * @param array $schemas
		 *
		 * @return IQuery
		 */
		public function uses(array $schemas): IQuery;

		/**
		 * @param string $alias
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function getSchema(string $alias): string;
	}
