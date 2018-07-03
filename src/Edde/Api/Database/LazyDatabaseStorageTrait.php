<?php
	declare(strict_types = 1);

	namespace Edde\Api\Database;

	/**
	 * Lazy database storage trait.
	 */
	trait LazyDatabaseStorageTrait {
		/**
		 * @var IDatabaseStorage
		 */
		protected $databaseStorage;

		/**
		 * @param IDatabaseStorage $databaseStorage
		 */
		public function lazyDatabaseStorage(IDatabaseStorage $databaseStorage) {
			$this->databaseStorage = $databaseStorage;
		}
	}
