<?php
	declare(strict_types=1);
	namespace Edde\Service\Storage;

	use Edde\Storage\IStorageFilterService;

	trait StorageFilterService {
		/** @var IStorageFilterService */
		protected $storageFilterService;

		/**
		 * @param IStorageFilterService $storageFilterService
		 */
		public function injectStorageFilterService(IStorageFilterService $storageFilterService): void {
			$this->storageFilterService = $storageFilterService;
		}
	}
