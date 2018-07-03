<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	use Edde\Api\Collection\IList;

	/**
	 * Formal content type implementation; content type can have additional parameters (thus extended from IList).
	 */
	interface IContentType extends IList {
		/**
		 * return mime type of this content type
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getMime(string $default = null);

		/**
		 * return charset parameter of this mime type
		 *
		 * @param string $default
		 *
		 * @return string
		 */
		public function getCharset(string $default = 'utf-8'): string;

		/**
		 * return set of parameters of content type
		 *
		 * @return array
		 */
		public function getParameterList(): array;

		/**
		 * return mime type of this content type
		 *
		 * @return string
		 */
		public function __toString(): string;
	}
