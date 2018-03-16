<?php
	declare(strict_types=1);
	namespace Edde\Api\Config;

	use Edde\Exception\Config\RequiredConfigException;
	use Edde\Exception\Config\RequiredSectionException;

	interface IConfigService extends IConfigurable {
		/**
		 * @param string $name
		 *
		 * @return ISection
		 *
		 * @throws RequiredSectionException
		 * @throws RequiredConfigException
		 */
		public function require(string $name): ISection;

		/**
		 * @param string $name
		 *
		 * @return ISection
		 *
		 * @throws RequiredConfigException
		 */
		public function optional(string $name): ISection;
	}
