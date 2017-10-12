<?php
	namespace Edde\Api\Utils;

		use Edde\Api\Config\IConfigurable;

		interface ICliUtils extends IConfigurable {
			/**
			 * parse an input array of parameters to a key=value form
			 *
			 * @param array $argv
			 *
			 * @return array
			 */
			public function getArgumentList(array $argv) : array;
		}
