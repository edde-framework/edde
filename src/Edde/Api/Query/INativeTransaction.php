<?php
	namespace Edde\Api\Query;

		interface INativeTransaction extends \IteratorAggregate {
			/**
			 * add a query to a transaction
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return INativeTransaction
			 */
			public function query(INativeQuery $nativeQuery): INativeTransaction;

			/**
			 * @return \Traversable|INativeQuery[]
			 */
			public function getIterator();
		}
