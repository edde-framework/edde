<?php
	declare(strict_types=1);
	namespace Edde\Api\Generator\Inject;

		use Edde\Api\Generator\IGeneratorManager;

		trait GeneratorManager {
			/**
			 * @var IGeneratorManager
			 */
			protected $generatorManager;

			/**
			 * @param IGeneratorManager $generatorManager
			 */
			public function lazyGeneratorManager(IGeneratorManager $generatorManager) {
				$this->generatorManager = $generatorManager;
			}
		}
