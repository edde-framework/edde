<?php
	declare(strict_types=1);
	namespace Edde\Api\Generator;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Generator\Exception\UnknownGeneratorException;

		interface IGeneratorManager extends IConfigurable {
			/**
			 * register the given generator
			 *
			 * @param string     $name
			 * @param IGenerator $generator
			 *
			 * @return IGeneratorManager
			 */
			public function registerGenerator(string $name, IGenerator $generator): IGeneratorManager;

			/**
			 * register list of generators
			 *
			 * @param IGenerator[] $generatorList
			 *
			 * @return IGeneratorManager
			 */
			public function registerGeneratorList(array $generatorList): IGeneratorManager;

			/**
			 * @param string $name
			 *
			 * @return IGenerator
			 *
			 * @throws UnknownGeneratorException
			 */
			public function getGenerator(string $name): IGenerator;

			/**
			 * take input array and generate values for all values known in this manager; only
			 * null values should be generated
			 *
			 * @param array $source
			 *
			 * @return array
			 */
			public function generate(array $source): array;
		}
