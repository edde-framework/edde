<?php
	declare(strict_types=1);
	namespace Edde\Inject\Generator;

	use Edde\Generator\IGeneratorManager;

	trait GeneratorManager {
		/**
		 * @var \Edde\Generator\IGeneratorManager
		 */
		protected $generatorManager;

		/**
		 * @param IGeneratorManager $generatorManager
		 */
		public function lazyGeneratorManager(IGeneratorManager $generatorManager) {
			$this->generatorManager = $generatorManager;
		}
	}
