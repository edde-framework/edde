<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	use Edde\Api\Config\IConfigurable;

	interface IConverterManager extends IConfigurable {
		/**
		 * register a converter
		 *
		 * @param IConverter $converter
		 * @param bool       $force
		 *
		 * @return IConverterManager
		 */
		public function registerConverter(IConverter $converter, bool $force = false): IConverterManager;

		/**
		 * magical method for generic data conversion; ideologically it is based on a mime type conversion, but identifiers can be arbitrary
		 *
		 * @param mixed  $content    generic input which will be converted in a generic output (defined by mime a target)
		 * @param string $mime       generic identifier, it can be formal mime type or anything else (but there must be known converter)
		 * @param array  $targetList list if targets; first registered would win; initial idea was http accept header
		 *
		 * @return IConvertable
		 */
		public function convert($content, string $mime, array $targetList): IConvertable;

		/**
		 * @param IContent $content
		 * @param array    $targetList
		 *
		 * @return IConvertable
		 */
		public function content(IContent $content, array $targetList = null): IConvertable;
	}
