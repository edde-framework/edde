<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;
	use IteratorAggregate;

	/**
	 * Collection of ICrate is the result of a query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * return collection query
		 *
		 * @return IQuery
		 */
		public function getQuery();
	}
