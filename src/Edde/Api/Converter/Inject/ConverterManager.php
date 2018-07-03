<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter\Inject;

	use Edde\Api\Converter\IConverterManager;

	/**
	 * Lazy dependency on a converter manager.
	 */
	trait ConverterManager {
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		/**
		 * @param IConverterManager $converterManager
		 */
		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}
	}
