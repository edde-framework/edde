<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface ISelectQuery extends IQuery {
		/**
		 * add a source schema to the query
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return ISelectQuery
		 */
		public function use(string $schema, string $alias = null): ISelectQuery;

		/**
		 * make use of the given schema names; [$alias => $schema]
		 *
		 * @param string[] $schemas
		 *
		 * @return ISelectQuery
		 */
		public function uses(array $schemas): ISelectQuery;

		/**
		 * attach is kind of "join" - it makes relation between schema aliases
		 *
		 * @param string $attach   source alias (who is being attached)
		 * @param string $to       target alias (target of an attach)
		 * @param string $relation alias used as a relation
		 *
		 * @return ISelectQuery
		 */
		public function attach(string $attach, string $to, string $relation): ISelectQuery;

		/**
		 * property equalization
		 *
		 * @param string $source source alias
		 * @param string $from   source property on source alias
		 * @param string $target target alias
		 * @param string $to     target property on target alias
		 *
		 * @return ISelectQuery
		 */
		public function equal(string $source, string $from, string $target, string $to): ISelectQuery;

		/**
		 * where equal to a value (not to an another property)
		 *
		 * @param string $alias    schema alias of a property
		 * @param string $property property of a source alias
		 * @param mixed  $value    simple scalar value
		 *
		 * @return ISelectQuery
		 */
		public function equalTo(string $alias, string $property, $value): ISelectQuery;

		/**
		 * @param string $alias
		 * @param string $property
		 * @param string $order
		 *
		 * @return ISelectQuery
		 */
		public function order(string $alias, string $property, string $order = 'asc'): ISelectQuery;

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
