<?php
	declare(strict_types=1);
	namespace Edde\Config;

	interface IConfigService extends IConfigurable {
		/**
		 * @param string $name
		 *
		 * @return ISection
		 *
		 * @throws ConfigException
		 */
		public function require(string $name): ISection;

		/**
		 * @param string $name
		 *
		 * @return ISection
		 *
		 * @throws ConfigException
		 */
		public function optional(string $name): ISection;
	}
