<?php
	namespace Edde\Api\Database\Inject;

		use Edde\Api\Database\IDatabaseStorage;

		trait DatabaseStorage {
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
