<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Formal interface for a cookie list implementation.
	 */
	interface ICookieList extends IConfigurable {
		/**
		 * set a cookie
		 *
		 * @param ICookie $cookie
		 *
		 * @return $this
		 */
		public function add(ICookie $cookie);

		/**
		 * setup cookies
		 *
		 * @return ICookieList
		 */
		public function setupCookieList(): ICookieList;
	}
