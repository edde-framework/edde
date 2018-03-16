<?php
	declare(strict_types=1);
	namespace Edde\Api\Config;

	use Edde\Config\ConfigException;
	use stdClass;

	interface IConfigLoader extends IConfigurable {
		/**
		 * @param string $config
		 *
		 * @return IConfigLoader
		 */
		public function require(string $config): IConfigLoader;

		/**
		 * @param string $config
		 *
		 * @return IConfigLoader
		 */
		public function optional(string $config): IConfigLoader;

		/**
		 * clear all config files
		 *
		 * @return IConfigLoader
		 */
		public function clear(): IConfigLoader;

		/**
		 * compile the stuff into an object
		 *
		 * @return stdClass
		 *
		 * @throws ConfigException
		 */
		public function compile(): stdClass;
	}
