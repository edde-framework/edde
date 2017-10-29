<?php
	namespace Edde\Api\Query;

		interface INativeBatch extends INativeQuery, \IteratorAggregate {
			/**
			 * add a query to batch
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return INativeBatch
			 */
			public function add(INativeQuery $nativeQuery): INativeBatch;

			/**
			 * shortcut for a new native query
			 *
			 * @param mixed $query
			 * @param array $parameterList
			 *
			 * @return INativeBatch
			 */
			public function addQuery($query, array $parameterList = []): INativeBatch;

			/**
			 * @return \Traversable|INativeQuery[]
			 */
			public function getIterator();
		}
