<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use stdClass;

	/**
	 * Just one "wide" row from storage (could be more tables joined together).
	 */
	interface IRow {
		/**
		 * @param string $alias
		 *
		 * @return stdClass
		 *
		 * @throws StorageException
		 */
		public function getItem(string $alias): stdClass;

		/**
		 * @return stdClass[]
		 */
		public function getItems(): array;
	}
