<?php
	namespace Edde\Api\Converter;

		use Edde\Api\Content\IContent;

		interface IConvertable {
			/**
			 * get content being converted
			 *
			 * @return IContent
			 */
			public function getContent() : IContent;

			/**
			 * converter choosen to convert the given input content to the output content
			 *
			 * @return IConverter
			 */
			public function getConverter() : IConverter;

			/**
			 * convert source content to the target content
			 *
			 * @return IContent
			 */
			public function convert() : IContent;
		}
