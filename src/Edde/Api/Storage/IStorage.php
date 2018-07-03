<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;

	/**
	 * This is abstracted way how to store (serialize) almost any object; storage can be arbitrary technology with ability to understand Edde's IQL.
	 */
	interface IStorage extends IDeffered {
		/**
		 * start a transaction
		 *
		 * @param bool $exclusive if true and there is already transaction, exception should be thrown
		 *
		 * @return IStorage
		 */
		public function start(bool $exclusive = false): IStorage;

		/**
		 * commit a transaciton
		 *
		 * @return IStorage
		 */
		public function commit(): IStorage;

		/**
		 * rollback a transaction
		 *
		 * @return IStorage
		 */
		public function rollback(): IStorage;

		/**
		 * execute the given query against this storage and return storage's native result
		 *
		 * @param IQuery $query
		 *
		 * @return mixed
		 */
		public function execute(IQuery $query);

		/**
		 * execute native query and return native result; this method should not be commonly used
		 *
		 * @param IStaticQuery $staticQuery
		 *
		 * @return mixed
		 */
		public function native(IStaticQuery $staticQuery);

		/**
		 * return collection based on the input query; if storage doesn't understand the queery, exception should be thrown
		 *
		 * @param string $crate of Crate
		 * @param IQuery $query
		 * @param string $schema
		 *
		 * @return ICrate[]|ICollection
		 */
		public function collection(string $crate, IQuery $query = null, string $schema = null): ICollection;

		/**
		 * helper method for a m:n crate collection
		 *
		 * @param ICrate $crate
		 * @param string $relation
		 * @param string $source
		 * @param string $target
		 * @param string $crateTo optional target crate class
		 *
		 * @return ICrate[]|ICollection
		 */
		public function collectionTo(ICrate $crate, string $relation, string $source, string $target, string $crateTo = null): ICollection;

		/**
		 * try to store the given crate
		 *
		 * @param ICrate $crate
		 *
		 * @return IStorage
		 */
		public function store(ICrate $crate): IStorage;

		/**
		 * retrieve crate by the given query; it should formally go through a collection method; if there is no such crate, exception should be thrown
		 *
		 * @param string $crate
		 * @param IQuery $query
		 * @param string $schema
		 *
		 * @return ICrate
		 */
		public function load(string $crate, IQuery $query, string $schema = null): ICrate;

		/**
		 * load crate by the given link name
		 *
		 * @param ICrate $crate
		 * @param string $name
		 *
		 * @return ICrate
		 */
		public function getLink(ICrate $crate, string $name): ICrate;
	}
