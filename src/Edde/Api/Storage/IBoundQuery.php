<?php
	declare(strict_types=1);

	namespace Edde\Api\Storage;

	use Edde\Api\Query\IQuery;

	/**
	 * Special case of a Query bound to particular storage.
	 */
	interface IBoundQuery {
		/**
		 * bind query to the given storage
		 *
		 * @param IQuery   $query
		 * @param IStorage $storage
		 *
		 * @return IBoundQuery
		 */
		public function bind(IQuery $query, IStorage $storage): IBoundQuery;

		/**
		 * execute method of a storage
		 *
		 * @return mixed
		 */
		public function execute();
	}
