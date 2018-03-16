<?php
	declare(strict_types=1);
	namespace Edde\Api\Config;

	use Edde\Exception\Config\RequiredConfigException;
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
		 * compile the stuff into an object
		 *
		 * @return stdClass
		 *
		 * @throws RequiredConfigException
		 */
		public function compile(): stdClass;
	}
