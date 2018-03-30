<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface ISelectQuery extends IQuery {
		/**
		 * add a source to the query
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return ISelectQuery
		 */
		public function use(string $schema, string $alias = null): ISelectQuery;

		/**
		 * @param array $schemas
		 *
		 * @return ISelectQuery
		 */
		public function uses(array $schemas): ISelectQuery;

		/**
		 * which alias should be returned by the query
		 *
		 * @param string $alias
		 *
		 * @return ISelectQuery
		 */
		public function return(string $alias): ISelectQuery;

		/**
		 * @param array $aliases
		 *
		 * @return ISelectQuery
		 */
		public function returns(array $aliases): ISelectQuery;

		/**
		 * @param string $alias
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function getSchema(string $alias): string;
	}
