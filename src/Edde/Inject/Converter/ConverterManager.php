<?php
	declare(strict_types=1);
	namespace Edde\Inject\Converter;

	use Edde\Api\Converter\IConverterManager;

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
