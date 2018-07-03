<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;
	use IteratorAggregate;

	/**
	 * Collection of crates created on demand.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * return schema of this collection
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * create a new crate; the crate should not be added to a collection
		 *
		 * @param array $push
		 *
		 * @return ICrate
		 */
		public function createCrate(array $push = null): ICrate;

		/**
		 * add crate to this collection
		 *
		 * @param ICrate $crate
		 *
		 * @return ICollection
		 */
		public function addCrate(ICrate $crate): ICollection;
	}
