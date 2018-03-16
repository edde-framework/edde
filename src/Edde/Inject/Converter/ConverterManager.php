<?php
	declare(strict_types=1);
	namespace Edde\Inject\Converter;

	use Edde\Converter\IConverterManager;

	trait ConverterManager {
		/**
		 * @var \Edde\Converter\IConverterManager
		 */
		protected $converterManager;

		/**
		 * @param \Edde\Converter\IConverterManager $converterManager
		 */
		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}
	}
