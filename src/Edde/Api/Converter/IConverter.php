<?php
	declare(strict_types=1);
	namespace Edde\Api\Converter;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Content\IContent;
	use Edde\Exception\Converter\UnsupportedConversionException;

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

		/**
		 * do the conversion; it could event be direct streamed conversion from one
		 * type to another being actually done when target content is iterated
		 *
		 * @param IContent    $content
		 * @param string|null $target
		 *
		 * @return IContent
		 *
		 * @throws UnsupportedConversionException
		 */
		public function convert(IContent $content, string $target = null): IContent;
	}
