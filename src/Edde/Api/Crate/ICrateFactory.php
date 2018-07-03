<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * This mechanism is inteded to use as conversion from an input data to crates.
	 */
	interface ICrateFactory extends IDeffered {
		/**
		 * is possible to create crate with the given name
		 *
		 * @param string $crate
		 *
		 * @return bool
		 */
		public function hasCrate(string $crate): bool;

		/**
		 * create crate with a given class (should be through container) and with the given schema
		 *
		 * @param string $crate
		 * @param string $schema
		 * @param array $load
		 *
		 * @return ICrate
		 */
		public function crate(string $crate, string $schema = null, array $load = null): ICrate;

		/**
		 * create crate collection
		 *
		 * @param string $schema
		 * @param string $crate
		 *
		 * @return ICollection
		 */
		public function collection(string $schema, string $crate = null): ICollection;

		/**
		 * build crate list from the input array
		 *
		 * @param array $crateList
		 *
		 * @return ICrate[]
		 */
		public function build(array $crateList): array;
	}
