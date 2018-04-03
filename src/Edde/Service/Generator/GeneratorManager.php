<?php
	declare(strict_types=1);
	namespace Edde\Service\Generator;

	use Edde\Generator\IGeneratorManager;

	trait GeneratorManager {
		/** @var IGeneratorManager */
		protected $generatorManager;

		/**
		 * @param IGeneratorManager $generatorManager
		 */
		public function injectGeneratorManager(IGeneratorManager $generatorManager) {
			$this->generatorManager = $generatorManager;
		}
	}
