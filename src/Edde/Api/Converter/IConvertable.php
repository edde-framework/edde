<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	interface IConvertable {
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
