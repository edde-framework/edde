<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

	/**
	 * Explicit interface for http header list.
	 */
		interface IHeaders {
			/**
			 * add a header; value could be even prepared helper object (like IContentType, ...)
			 *
			 * @param string $name
			 * @param mixed  $value
			 *
			 * @return IHeaders
			 */
			public function add(string $name, $value): IHeaders;

			/**
			 * return a simple array with headers; if there is more values per one headers,
			 * they would added to sub array
			 *
			 * @return array
			 */
			public function toArray(): array;
		}
