<?php
	declare(strict_types=1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Schema\ISchema;

	/**
	 * Repository is simple type of storage, intended to be used as storage endpoint for
	 * services (for example user service will be extended from this interface).
	 */
	interface IRepository {
		/**
		 * repository is living over some schema
		 *
		 * @param ISchema $schema
		 *
		 * @return IRepository
		 */
		public function setSchema(ISchema $schema): IRepository;

		/**
		 * try to store the given crate
		 *
		 * @param ICrate $crate
		 *
		 * @return IRepository
		 */
		public function store(ICrate $crate): IRepository;

		/**
		 * create the given query (through container)
		 *
		 * @param string $query
		 * @param array  $parameterList
		 *
		 * @return IBoundQuery
		 */
		public function bound(string $query, ...$parameterList): IBoundQuery;

		/**
		 * select query by default (method over bound())
		 *
		 * @return IBoundQuery
		 */
		public function query(): IBoundQuery;
	}
