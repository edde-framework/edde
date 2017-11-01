<?php
	namespace Edde\Common\Generator;

		use Edde\Api\Generator\Exception\UnknownGeneratorException;
		use Edde\Api\Generator\IGenerator;
		use Edde\Api\Generator\IGeneratorManager;
		use Edde\Common\Object\Object;

		class GeneratorManager extends Object implements IGeneratorManager {
			/**
			 * @var IGenerator[]
			 */
			protected $generatorList = [];

			/**
			 * @inheritdoc
			 */
			public function registerGenerator(string $name, IGenerator $generator): IGeneratorManager {
				$this->generatorList[$name] = $generator;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerGeneratorList(array $generatorList): IGeneratorManager {
				foreach ($generatorList as $name => $generator) {
					$this->registerGenerator($name, $generator);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getGenerator(string $name): IGenerator {
				if (isset($this->generatorList[$name]) === false) {
					throw new UnknownGeneratorException(sprintf('Requested unknown generator [%s].', $name));
				}
				return $this->generatorList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function generate(array $source): array {
				$result = $source;
				foreach ($source as $k => $v) {
					if (isset($this->generatorList[$k]) && $v === null) {
						$result[$k] = $this->generatorList[$k]->generate();
					}
				}
				return $result;
			}
		}
