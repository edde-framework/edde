<?php
	declare(strict_types=1);
	namespace Edde\Http;

	/**
	 * Formal content type implementation.
	 */
	interface IContentType {
		/**
		 * return mime type of this content type
		 *
		 * @return string
		 */
		public function getMime(): string;

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
		public function getParameters(): array;

		/**
		 * return mime type of this content type
		 *
		 * @return string
		 */
		public function __toString(): string;
	}
