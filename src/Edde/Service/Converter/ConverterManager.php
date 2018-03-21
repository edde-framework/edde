<?php
	declare(strict_types=1);
	namespace Edde\Service\Converter;

	use Edde\Converter\IConverterManager;

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
