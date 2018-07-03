<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	interface ICookieFactory {
		/**
		 * @return ICookieList
		 */
		public function create(): ICookieList;
	}
