<?php
	namespace Edde\Api\Converter;

		use Edde\Api\Config\IConfigurable;

		/**
		 * A Converter is an implementation of converter from one type to another one; the core
		 * ide is that one converter should to just one type of conversions (like json_encode), including
		 * different names for it's type, but no more conversions (like one converter do json_encode, decode,
		 * serialize, ...).
		 */
		interface IConverter extends IConfigurable {
			/**
			 * return an array with types supported as an input type (for example it could be
			 * string, text/plain, ... for target application/json)
			 *
			 * @return string[]
			 */
			public function getSourceList(): array;

			/**
			 * return an array with supported target conversion
			 *
			 * @return string[]
			 */
			public function getTargetList(): array;
		}
