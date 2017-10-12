<?php
	namespace Edde\Api\Converter;

		use Edde\Api\Config\IConfigurable;

		interface IConverter extends IConfigurable {
			/**
			 * return an array with types supported as an input type (for example it could be
			 * string, text/plain, ... for target application/json)
			 *
			 * @return string[]
			 */
			public function getSourceList() : array;

			/**
			 * return an array with supported target conversion
			 *
			 * @return string[]
			 */
			public function getTargetList() : array;
		}
