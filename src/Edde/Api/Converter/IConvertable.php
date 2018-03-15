<?php
	declare(strict_types=1);
	namespace Edde\Api\Converter;

	use Edde\Api\Content\IContent;

	interface IConvertable {
		/**
		 * who will do the conversion
		 *
		 * @return IConverter
		 */
		public function getConverter(): IConverter;

		/**
		 * return subject content
		 *
		 * @return IContent
		 */
		public function getContent(): IContent;

		/**
		 * return target mime type; if target is not specified, source should not be converted
		 *
		 * @return string|null
		 */
		public function getTarget();

		/**
		 * try to convert an input
		 *
		 * @return IContent
		 */
		public function convert(): IContent;
	}
