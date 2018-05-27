<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IQuery {
		/**
		 * add a source schema to the query
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return IQuery
		 */
		public function select(string $schema, string $alias = null): IQuery;

		/**
		 * make use of the given schema names; [$alias => $schema]
		 *
		 * @param string[] $schemas
		 *
		 * @return IQuery
		 */
		public function selects(array $schemas): IQuery;

		/**
		 * attach is kind of "join" - it makes relation between schema aliases
		 *
		 * @param string $attach   source alias (who is being attached)
		 * @param string $to       target alias (target of an attach)
		 * @param string $relation alias used as a relation
		 *
		 * @return IQuery
		 */
		public function attach(string $attach, string $to, string $relation): IQuery;

		/**
		 * property equalization
		 *
		 * @param string $source source alias
		 * @param string $from   source property on source alias
		 * @param string $target target alias
		 * @param string $to     target property on target alias
		 *
		 * @return IQuery
		 */
		public function equal(string $source, string $from, string $target, string $to): IQuery;

		/**
		 * where equal to a value (not to an another property)
		 *
		 * @param string $alias    schema alias of a property
		 * @param string $property property of a source alias
		 * @param mixed  $value    simple scalar value
		 *
		 * @return IQuery
		 */
		public function equalTo(string $alias, string $property, $value): IQuery;

		/**
		 * @param string $alias
		 * @param string $property
		 * @param string $order
		 *
		 * @return IQuery
		 */
		public function order(string $alias, string $property, string $order = 'asc'): IQuery;

		/**
		 * which alias should be returned by the query
		 *
		 * @param string $alias
		 *
		 * @return IQuery
		 */
		public function return(string $alias): IQuery;

		/**
		 * @param array $aliases
		 *
		 * @return IQuery
		 */
		public function returns(array $aliases): IQuery;

		/**
		 * @param string $alias
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function getSelect(string $alias): string;

		/**
		 * return schemas used in this query; value could be duplicated as an array is [alias => value]
		 *
		 * @return string[]
		 */
		public function getSelects(): array;
	}
