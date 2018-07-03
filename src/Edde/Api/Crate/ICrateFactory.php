<?php
	declare(strict_types=1);

	namespace Edde\Api\Crate;

	use Edde\Api\Config\IConfigurable;

	/**
	 * This mechanism is inteded to use as conversion from an input data to crates.
	 */
	interface ICrateFactory extends IConfigurable {
		/**
		 * create crate with a given class (should be through container) and with the given schema
		 *
		 * @param string $schema
		 * @param array  $load
		 * @param string $crate
		 *
		 * @return ICrate
		 */
		public function crate(string $schema, array $load = null, string $crate = null): ICrate;

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
