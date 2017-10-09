<?php
	declare(strict_types=1);
	namespace Edde\Api\Utils;

		use Edde\Api\Config\IConfigurable;

		/**
		 * String utils interface.
		 */
		interface IStringUtils extends IConfigurable {
			/**
			 * preg_match on steroids
			 *
			 * @param string $string
			 * @param string $pattern
			 * @param bool   $named
			 * @param bool   $trim
			 *
			 * @return array|null
			 */
			public function match(string $string, string $pattern, bool $named = false, bool $trim = false);
		}
