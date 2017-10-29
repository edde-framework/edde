<?php
	namespace Edde\Api\Query;

		interface INativeBatch extends \IteratorAggregate {
			/**
			 * add a query to batch
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return INativeBatch
			 */
			public function add(INativeQuery $nativeQuery): INativeBatch;

			/**
			 * @return \Traversable|INativeQuery[]
			 */
			public function getIterator();
		}
