<?php
	declare(strict_types=1);
	namespace Edde\Service\Generator;

	use Edde\Api\Generator\IGenerator;
	use Edde\Api\Generator\IGeneratorManager;
	use Edde\Generator\GeneratorException;
	use Edde\Object;

	class GeneratorManager extends Object implements IGeneratorManager {
		/** @var IGenerator[] */
		protected $generators = [];

		/** @inheritdoc */
		public function registerGenerator(string $name, IGenerator $generator): IGeneratorManager {
			$this->generators[$name] = $generator;
			return $this;
		}

		/** @inheritdoc */
		public function registerGenerators(array $generators): IGeneratorManager {
			foreach ($generators as $name => $generator) {
				$this->registerGenerator($name, $generator);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getGenerator(string $name): IGenerator {
			if (isset($this->generators[$name]) === false) {
				throw new GeneratorException(sprintf('Requested unknown generator [%s].', $name));
			}
			return $this->generators[$name];
		}

		/** @inheritdoc */
		public function generate(array $source): array {
			$result = $source;
			foreach ($source as $k => $v) {
				if (isset($this->generators[$k]) && $v === null) {
					$result[$k] = $this->generators[$k]->generate();
				}
			}
			return $result;
		}
	}
